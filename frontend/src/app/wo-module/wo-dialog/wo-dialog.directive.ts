import { Directive, ViewContainerRef } from '@angular/core';

@Directive({
    selector: '[appWoDialogHost]',
})
export class WoDialogDirective {
    constructor(public viewContainerRef: ViewContainerRef) { }
}
