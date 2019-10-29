import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '../../wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '../../core/meta-page.service';
import { ApiGeneralService } from '@app/share/api-general.service';
import { AuthService } from '@app/auth/auth.service';

@Component({
    templateUrl: 'confirm-registration.component.html',
    styleUrls: ['confirm-registration.component.css']
})
export class ConfirmRegistrationComponent implements OnInit {
    token: string;
    hasError = false;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiGeneralService,
        private auth: AuthService,
    ) {}

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.token = params['token'] || '';
            this.doCheckToken();
        });
    }

    doCheckToken() {
        this.api.send('auth/confirm-registration', {token: this.token}, {}).then(res => {
            if (res['status'] === 200) {
                this.auth.setToken(res['access_token'], res['expires_in']);
                this.auth.setRefreshToken(res['refresh_token']);
                this.auth.setUser(res['user']);
                this.router.navigate(['/success-registration']);
            } else {
                this.hasError = true;
            }
        });
    }
}
