import { Component, Input, AfterViewInit, ElementRef, ViewChild} from '@angular/core';
import { IWoDialogComponent } from '../wo-dialog.interface';

@Component({
    template: `
        <div class="wo-dialog-prompt__body">
            {{message}}
        </div>
        <div class="wo-dialog-prompt__input">
            <input class="form-control" type="text" #inputEl (keydown)="onKeyDown($event)" [(ngModel)]="value"/>
        </div>
        <div class="wo-dialog-prompt__buttons">
            <a class="btn btn-default" href="javascript:void(0);" (click)="close(false)">Cancel</a>
            <a class="btn btn-primary" href="javascript:void(0);" (click)="close(true)">OK</a>
        </div>
    `,
    styles: [`
        .wo-dialog-prompt__body {
            padding: 35px 15px 5px;
        }
        .wo-dialog-prompt__input {
            padding-left: 15px;
            padding-bottom: 15px;
            padding-right: 15px;
        }
        .wo-dialog-prompt__input input[type=text] {
            width: 100%;
        }
        .wo-dialog-prompt__buttons {
            padding: 8px 10px 8px;
            text-align: right;
            border-top: 1px solid #eaeaea;
        }
        .wo-dialog-prompt__buttons a {
            min-width: 50px;
        }
    `],
})
export class WoDialogPromptComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    message = '';
    value = '';

    @ViewChild('inputEl', {read: ElementRef, static: false}) inputEl: ElementRef;

    ngAfterViewInit () {
        if (this.dialog.options && this.dialog.options.message) {
            this.message = this.dialog.options.message;
        }

        this.inputEl.nativeElement.focus();
    }

    onKeyDown(event) {
        if (event['keyCode'] === 13) {
            this.close(true);
        }
    }

    close(result) {
        if (result === true) {
            if (this.value !== '') {
                this.dialog.close(this.value);
            }
        } else {
            this.dialog.close(false);
        }
    }
}
