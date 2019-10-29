import { Component, Input, AfterViewInit} from '@angular/core';
import { IWoDialogComponent } from '../wo-dialog.interface';

@Component({
    template: `
        <div class="wo-dialog-confirm__body">
            {{message}}
        </div>
        <div class="wo-dialog-confirm__buttons">
            <a class="btn btn-default" href="javascript:void(0);" (click)="close(false)">Cancel</a>
            <a class="btn btn-primary" href="javascript:void(0);" (click)="close(true)">OK</a>
        </div>
    `,
    styles: [`
        .wo-dialog-confirm__body {
            padding: 35px 15px 25px;
        }
        .wo-dialog-confirm__buttons {
            padding: 8px 10px 8px;
            text-align: right;
            border-top: 1px solid #eaeaea;
        }
        .wo-dialog-confirm__buttons a {
            min-width: 50px;
        }
    `],
})
export class WoDialogConfirmComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    message = '';

    ngAfterViewInit () {
        if (this.dialog.options && this.dialog.options.message) {
            this.message = this.dialog.options.message;
        }
    }

    close(result) {
        this.dialog.close(result);
    }
}
