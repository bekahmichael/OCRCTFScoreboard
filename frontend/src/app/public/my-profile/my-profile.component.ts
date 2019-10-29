import { Component } from '@angular/core';
import { AuthService } from '@app/auth/auth.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { ApiMemberService } from '@app/share/api-member.service';

@Component({
    templateUrl: 'my-profile.component.html',
    styleUrls: ['my-profile.component.css']
})
export class MyProfileComponent {
    user: any;
    dataErrors: any;

    constructor (
        private api: ApiMemberService,
        private auth: AuthService,
        private woFlash: WoFlashService,
        private metaPage: MetaPageService,
    ) {
        this.metaPage.setTitle('My Profile');
        this.api.send('auth/profile').then(res => {
            this.user = res['data'];
        });
    }

    doSave() {
        this.woFlash.hide();
        this.dataErrors = {};
        this.api.send('auth/profile', {}, this.user).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                }, 200);
            } else {
                setTimeout(() => {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.woFlash.show('my-profile');
                    this.auth.user['first_name'] = this.user['first_name'];
                    this.auth.user['last_name'] = this.user['last_name'];
                    this.auth.user['middle_name'] = this.user['middle_name'];
                    this.auth.setUser(this.auth.user);
                }, 100);
                this.dataErrors = {};
            }
        });
    }
}
