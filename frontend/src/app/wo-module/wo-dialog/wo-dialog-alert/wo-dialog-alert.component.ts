import { Component, Input, AfterViewInit} from '@angular/core';
import { IWoDialogComponent } from '../wo-dialog.interface';

@Component({
    template: `
        <div class="wo-dialog-alert__body">
            {{message}}
        </div>
        <div class="wo-dialog-alert__buttons">
            <a class="btn btn-primary" href="javascript:void(0);" (click)="close()">OK</a>
        </div>
    `,
    styles: [`
        .wo-dialog-alert__body {
            padding: 35px 15px 25px;
        }
        .wo-dialog-alert__buttons {
            padding: 8px 10px 8px;
            text-align: right;
            border-top: 1px solid #eaeaea;
        }
        .wo-dialog-alert__buttons a {
            min-width: 50px;
        }
    `],
})
export class WoDialogAlertComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    message = '';

    ngAfterViewInit () {
        if (this.dialog.options && this.dialog.options.message) {
            this.message = this.dialog.options.message;
        }
    }

    close() {
        this.dialog.close(false);
    }
}
