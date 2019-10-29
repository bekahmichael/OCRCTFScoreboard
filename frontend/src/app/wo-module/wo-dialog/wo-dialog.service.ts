import { Injectable } from '@angular/core';
import { WoDialog } from './wo-dialog';

@Injectable()
export class WoDialogService {
    static instance: WoDialogService;
    dialogItems = [];

    constructor() {
        return WoDialogService.instance = WoDialogService.instance || this;
    }

    open (component, options) {
        document.body.classList.add('modal-open');

        if ('activeElement' in document) {
            document.activeElement['blur']();
        }

        const ref = new WoDialog(component, options, this);

        this.dialogItems.push(ref);
        return ref;
    }

    close (dialog, result) {
        this.dialogItems.splice(this.dialogItems.indexOf(dialog), 1);
        if (this.dialogItems.length < 1) {
            document.body.classList.remove('modal-open');
        }
    }

    closeAll(result) {
        for (let i = this.dialogItems.length - 1; i >= 0; i--) {
            this.dialogItems[i].close(result);
        }
    }
}
