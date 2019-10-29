import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { MetaPageService } from '../../core/meta-page.service';
import { ApiGeneralService } from '@app/share/api-general.service';

@Component({
    templateUrl: 'signup.component.html',
    styleUrls: ['signup.component.css'],
})
export class SignupComponent implements OnInit {
    dataForm = {};
    formErrors = {};

    isConfirm = 0;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private api: ApiGeneralService,
    ) {
        this.metaPage.setTitle('Sign Up');
    }

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.isConfirm = parseInt(params['confirm'], 10) || 0;
        });
    }

    doSubmit() {
        this.api.send('auth/signup', {}, this.dataForm).then(res => {
            if (res['errors']) {
                this.formErrors = res['errors'];
            } else {
                this.formErrors = {};
                this.router.navigate(['/signup', {confirm: 1}]);
            }
        });
    }
}
