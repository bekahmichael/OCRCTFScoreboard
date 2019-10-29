import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { AuthService } from '@app/auth/auth.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

@Component({
  templateUrl: 'user-detail.component.html',
  styleUrls: ['user-detail.component.css'],
})
export class UserDetailComponent implements OnInit {
    dataUser = {
        id: 0,
        first_name: '',
        last_name: '',
        middle_name: '',
        email: '',
        password: '',
        status: 'active',
        role: 'admin',
        username: '',
        password_repeat: '',
    };

    dataErrors = {};
    dataMessages = [];

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private auth: AuthService,
        private woFlash: WoFlashService,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiAdminService,
    ) {
        this.metaPage.setTitle('User Profile');

        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {iconClass: 'fa fa-user-secret', route: '/admin/users', text: 'Users'},
            {text: 'User Profile'}
        ]);
    }

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.woFlash.show();
            this.loadUser(parseInt(params['id'], 10) || 0);
        });
    }

    loadUser(id) {
        this.dataUser.id = id;
        if (this.dataUser.id > 0) {
            this.api.send('user/profile', {id: this.dataUser.id}).then(res => {
                this.dataUser.first_name  = res['data'].first_name;
                this.dataUser.last_name   = res['data'].last_name;
                this.dataUser.middle_name = res['data'].middle_name;
                this.dataUser.email       = res['data'].email;
                this.dataUser.status      = res['data'].status;
                this.dataUser.username    = res['data'].username;
                this.dataUser.role        = res['data'].role;
            });
        }
    }

    onSave () {
        this.woFlash.hide();
        this.api.send('user/profile', {id: this.dataUser.id}, {data: this.dataUser}).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                if (this.dataUser.id === 0) {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.router.navigate(['/admin/users', res['data'].id]);
                } else {
                    if (this.dataUser.id === parseInt(this.auth.user['id'], 10)) {
                        this.auth.user['first_name'] = this.dataUser.first_name;
                        this.auth.user['last_name'] = this.dataUser.last_name;
                    }

                    this.dataErrors = {};
                    setTimeout(() => {
                        this.woFlash.addMessage('The operation was successfully done!');
                        this.woFlash.show();
                    }, 100);
                }
            }
        });
    }

    doCancel(event) {
        window.history.back();
        event.preventDefault();
    }
}
