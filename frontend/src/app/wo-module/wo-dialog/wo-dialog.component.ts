import { Component, ViewChild, ComponentFactoryResolver, AfterViewInit, Input, ElementRef } from '@angular/core';
import { WoDialogDirective } from './wo-dialog.directive';
import { IWoDialogComponent } from './wo-dialog.interface';

@Component({
    selector: 'wo-dialog',
    template: `
        <div class="c-wo-dialog" #woDialogEl [ngStyle]="dialog?.options?.css">
            <div class="c-wo-dialog__content">
                <a href="javascript:void(0);" class="c-wo-dialog__close-btn" (click)="close()">×</a>
                <ng-template appWoDialogHost></ng-template>
            </div>
        </div>
    `,
    styleUrls: ['wo-dialog.component.css'],
})
export class WoDialogComponent implements AfterViewInit {
    @ViewChild(WoDialogDirective, {static: false}) woDialogHost: WoDialogDirective;
    @Input() dialog;
    @ViewChild('woDialogEl', {read: ElementRef, static: false}) woDialogEl: ElementRef;

    options: {};

    constructor (
        private _componentFactoryResolver: ComponentFactoryResolver,
    ) {}

    ngAfterViewInit () {
        setTimeout(() => {
            this.woDialogEl.nativeElement.className += ' loaded';
            const componentFactory = this._componentFactoryResolver.resolveComponentFactory(this.dialog.component);
            const viewContainerRef = this.woDialogHost.viewContainerRef;
            viewContainerRef.clear();

            const componentRef = viewContainerRef.createComponent(componentFactory);
            (<IWoDialogComponent>componentRef.instance).dialog = this.dialog;
            componentRef.changeDetectorRef.detectChanges();
        }, 0);
    }

    close () {
        this.dialog.close(null);
    }
}
