import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiGeneralService } from '@app/share/api-general.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';

@Component({
    templateUrl: 'request-password-reset.component.html',
    styleUrls: ['request-password-reset.component.css']
})
export class RequestPasswordResetComponent {
    dataForm = {};
    formErrors = {};

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private api: ApiGeneralService,
        private woFlash: WoFlashService,
    ) {
        this.metaPage.setTitle('Request Password Reset');
    }

    doSubmit() {
        this.woFlash.hide();
        this.api.send('auth/forgot-password', {email: this.dataForm['email'] || ''}, {}).then(res => {
            if (res['errors']) {
                this.formErrors = res['errors'];
            } else {
                this.dataForm['email'] = '';
                this.formErrors = {};
                setTimeout(() => {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.woFlash.show();
                }, 100);
            }
        });
    }
}
