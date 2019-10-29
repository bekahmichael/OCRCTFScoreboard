import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';

@Injectable()
export class WoFlashService {
    static instance: WoFlashService;
    errorMessages = [];
    successMessages = [];

    flashchange = new BehaviorSubject<Object>(false);

    constructor() {
        return WoFlashService.instance = WoFlashService.instance || this;
    }

    addError (msg: any) {
        if (typeof msg === 'string') {
            this.errorMessages.push(msg);
        } else if (typeof msg === 'object') {
            Object.keys(msg).map(key => {
                this.errorMessages.push(msg[key]);
            });
        }
    }

    addMessage (msg: any) {
        if (typeof msg === 'string') {
            this.successMessages.push(msg);
        } else if (typeof msg === 'object') {
            Object.keys(msg).map(key => {
                this.successMessages.push(msg[key]);
            });
        }
    }

    show(block = 'main', scroll = false) {
        if (scroll === true) {
            window.scrollTo(0, 0);
        }

        this.flashchange.next({
            errors: this.errorMessages,
            success: this.successMessages,
            block: block
        });

        this.flush();
    }

    hide() {
        this.flashchange.next({
            errors: [],
            success: []
        });
    }

    flush () {
        this.errorMessages = [];
        this.successMessages = [];
    }

    geFlashChange() {
        return this.flashchange;
    }
}
