import { Component } from '@angular/core';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '../../core/meta-page.service';

@Component({
    templateUrl: 'page-not-found.componetn.html',
    styleUrls: ['page-not-found.component.css'],
})
export class PageNotFoundComponent {
    constructor (
        private breadcrumbs: WoBreadCrumbsService,
        private metaPage: MetaPageService,
    ) {
        this.metaPage.setTitle('404 page not found');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '', text: 'Home'},
            {text: '404 Error Page'}
        ]);
    }
}
