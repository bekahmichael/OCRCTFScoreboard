import { Component, Input } from '@angular/core';
import { WoFlashService } from './wo-flash.service';

@Component({
    selector: 'wo-flash',
    templateUrl: 'wo-flash.component.html',
    styleUrls: ['wo-flash.component.css'],
})
export class WoFlashComponent {
    @Input() block = 'main';
    errorPool = [];
    successPool = [];

    constructor (private woFlash: WoFlashService) {
        woFlash.geFlashChange().subscribe((props) => { this.doRender(props); });
    }

    doRender (props) {
        const data = {
            errors: this.woFlash.errorMessages,
            success: this.woFlash.successMessages
        };

        this.errorPool = [];
        this.successPool = [];

        if (props.block === this.block) {
            if (data && data['errors'] && data['errors'][0]) {
                this.errorPool = data['errors'];
            } else if (data && data['success'] && data['success'][0]) {
                this.successPool = data['success'];
            }
        }
    }

    doFlush() {
        this.errorPool = [];
        this.successPool = [];
        this.woFlash.flush();
    }
}
