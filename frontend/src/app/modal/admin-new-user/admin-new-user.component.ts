import { Component, Input, AfterViewInit} from '@angular/core';
import { IWoDialogComponent } from '../../wo-module/wo-dialog/wo-dialog.interface';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { AuthService } from '@app/auth/auth.service';
import { ApiAdminService } from '@app/share/api-admin.service';

@Component({
    templateUrl: 'admin-new-user.component.html',
    styleUrls: ['admin-new-user.component.css'],
})
export class AdminNewUserComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
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
        private auth: AuthService,
        private woFlash: WoFlashService,
        private api: ApiAdminService,
    ) {}

    ngAfterViewInit () {
        this.dialog.options.css = {width: '550px'};
        this.dataUser.id = parseInt(this.dialog.options.id, 10) || 0;
        this.dataUser.role = this.dialog.options.role;
        if (this.dataUser.id > 0) {
            this.loadUser(this.dataUser.id);
        }
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
                    this.woFlash.show('userlist');
                    this.dialog.close(false);
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

    close () {
        this.dialog.close(false);
    }

    doCancel(event) {
        this.dialog.close(false);
        event.preventDefault();
    }
}
