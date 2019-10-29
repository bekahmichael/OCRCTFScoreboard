import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiQuizService } from '@app/share/api-quiz.service';

@Component({
    templateUrl: 'login.component.html',
    styleUrls: ['login.component.css']
})
export class LoginComponent {
    pin = '';
    formErrors: any;
    isLoading = false;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiQuizService,
    ) {
    }

    doLogin() {
        if (this.isLoading === true) {
            return false;
        }
        this.isLoading = true;
        this.formErrors = {};

        this.api.send('quiz/access', {}, {pin: this.pin}).then((res) => {
            console.log('RESULT: ', res);
            this.isLoading = false;
            if (!res['errors']) {
                this.router.navigate(['/quiz/' + res['team_id'] + ':' + res['access_key']]);
            } else {
                setTimeout(() => {
                    this.formErrors = res['errors'];
                }, 200);
            }
        }).catch(res => {
            this.formErrors = {};
            this.isLoading = false;
            alert('Oops! Something went wrong.');
        });
    }
}
