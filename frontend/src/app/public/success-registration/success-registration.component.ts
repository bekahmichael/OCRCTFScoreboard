import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '../../wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '../../core/meta-page.service';

@Component({
    templateUrl: 'success-registration.component.html',
    styleUrls: ['success-registration.component.css']
})
export class SuccessRegistrationComponent {
    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
    ) {}
}
