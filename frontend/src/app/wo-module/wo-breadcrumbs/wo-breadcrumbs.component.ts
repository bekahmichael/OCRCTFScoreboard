import { Component } from '@angular/core';
import { WoBreadCrumbsService } from './wo-breadcrumbs.service';

@Component({
    selector: 'wo-breadcrumbs',
    templateUrl: 'wo-breadcrumbs.component.html',
    styleUrls: ['wo-breadcrumbs.component.css'],
})
export class WoBreadcrumbsComponent {
    constructor (public breadCrumbs: WoBreadCrumbsService ) {}
}
