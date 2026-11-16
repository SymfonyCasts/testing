import { Controller } from '@hotwired/stimulus';

/*
 * Runs a full diagnostic: fetches the ship's systems from the API and
 * reveals them one at a time, like a real scan.
 */
export default class extends Controller {
    static targets = ['button', 'output'];
    static values = {
        url: String,
        delay: { type: Number, default: 400 },
    };

    async run() {
        this.buttonTarget.disabled = true;
        this.outputTarget.replaceChildren();
        this.#printLine('> Scanning all systems...', 'text-sky-300');

        const response = await fetch(this.urlValue, { headers: { Accept: 'application/json' } });
        const ship = await response.json();
        let faults = 0;

        for (const system of ship.systems) {
            await this.#pause();

            if (system.status === 'fault') {
                faults++;
            }

            this.#printSystem(system);
        }

        await this.#pause();
        this.#printSummary(faults);
        this.buttonTarget.disabled = false;
    }

    #printSystem(system) {
        const line = document.createElement('li');
        line.className = 'flex items-end';

        const name = document.createElement('span');
        name.textContent = system.type.replaceAll('_', ' ').toUpperCase();

        const leader = document.createElement('span');
        leader.className = 'readout-leader';

        const status = document.createElement('span');
        const isOk = system.status === 'ok';
        status.textContent = isOk ? 'OK' : 'FAULT';
        status.className = isOk ? 'font-bold text-emerald-400' : 'font-bold text-rose-400';

        line.append(name, leader, status);
        this.outputTarget.append(line);
    }

    #printSummary(faults) {
        if (faults === 0) {
            this.#printLine('> ALL SYSTEMS OK', 'mt-3 font-bold text-emerald-400');

            return;
        }

        this.#printLine(`> ${faults} ${faults === 1 ? 'FAULT' : 'FAULTS'} DETECTED`, 'mt-3 font-bold text-rose-400');
    }

    #printLine(text, className) {
        const line = document.createElement('li');
        line.className = className;
        line.textContent = text;
        this.outputTarget.append(line);
    }

    #pause() {
        return new Promise((resolve) => setTimeout(resolve, this.delayValue));
    }
}
