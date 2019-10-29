import { Component, HostListener } from '@angular/core';
import { WoDialogService } from './wo-dialog.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-wo-dialogs',
    template: `
        <div class="c-wo-dialogs" [class.dialog-open]="dialog.dialogItems.length">
            <div (click)="onMouseDown($event)" class="c-wo-dialog_item" *ngFor="let dialogItem of dialog.dialogItems; index as di">
                <wo-dialog [attr.data-dialog-id]="di" [dialog]="dialogItem"></wo-dialog>
            </div>
        </div>
        <div *ngIf="dialog.dialogItems.length > 0" class="c-wo-dialogs__bg"></div>
    `,
    styleUrls: ['wo-dialogs.component.css'],
})
export class WoDialogsComponent {
    @HostListener('window:keydown', ['$event']) onKeydown (event) {
        if (this.dialog.dialogItems.length > 0) {
            if (event.keyCode === 27) { // Esc
                this.dialog.dialogItems[this.dialog.dialogItems.length - 1].close(null);
            }
        }
    }

    onMouseDown (event) {
        if (this.dialog.dialogItems.length > 0) {
            const lastIndex = this.dialog.dialogItems.length - 1;
            const elemRef = document.querySelector('[data-dialog-id="' + lastIndex + '"] .c-wo-dialog');

            const clickedInside = elemRef.contains(event.target);

            if (!clickedInside && document.contains(event.target)) {
                this.dialog.dialogItems[lastIndex].close(null);
            }
        }
    }

    constructor (public dialog: WoDialogService, private router: Router) {
        this.router.events.subscribe(params => {
            this.dialog.closeAll(null);
        });
    }
}
