import { Type } from '@angular/core';
import { Subject } from 'rxjs/Subject';

export class WoDialog {
    /** Subject for notifying the user that the dialog has finished closing. */
    private _afterClosed: Subject<any> = new Subject();

    constructor(public component: Type<any>, public options: any, public service) {}

    close (result: any) {
        this._afterClosed.next(result);
        this._afterClosed.complete();
        this.service.close(this, result);
    }

    afterClosed() {
        return this._afterClosed;
    }
}
