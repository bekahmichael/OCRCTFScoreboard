import { Component, Input, AfterViewInit} from '@angular/core';
import { IWoDialogComponent } from '../../wo-module/wo-dialog/wo-dialog.interface';
import { ApiAdminService } from '@app/share/api-admin.service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogAlertComponent } from '@app/wo-module/wo-dialog/wo-dialog-alert/wo-dialog-alert.component';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';

@Component({
    templateUrl: 'admin-access-to-teams.component.html',
    styleUrls: ['admin-access-to-teams.component.css'],
})
export class AdminAccessToTeamsComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    eventId: number = null;
    teams = [];
    allChecked = true;

    isProcess = false;
    currIndex = 0;

    constructor (
        private api: ApiAdminService,
        private dialogService: WoDialogService,
        private woFlash: WoFlashService,
    ) {}

    ngAfterViewInit () {
        this.dialog.options.css = {
            width: '500px',
        };
        this.eventId = this.dialog.options.eventId;

        this.loadData();
    }

    close () {
        this.isProcess = false;
        this.dialog.close(false);
    }

    loadData() {
        this.api.send('events/send-access-to-teams', {event_id: this.eventId}).then(res => {
            res['teams'].forEach(el => {
                el.is_checked = true;
                el.status  = 'N/A';
                el.is_ok   = false;
                el.is_fail = false;
            });
            this.teams = res['teams'];
        });
    }

    doSend() {
        let count = 0;

        this.woFlash.hide();

        this.teams.forEach(el => {
            if (el.is_checked === true) {
                count++;
            }
            el.status  = 'N/A';
            el.is_ok   = false;
            el.is_fail = false;
        });

        if (count === 0) {
            setTimeout(() => {
                this.dialogService.open(WoDialogAlertComponent, {message: 'You must select team(s).', css: {top: '130px'}});
            }, 10);
        } else {
            this.currIndex = 0;
            this.isProcess = true;
            this.sendEmail();
        }
    }

    sendEmail() {
        if (this.isProcess === true) {
            if (this.currIndex < this.teams.length) {
                if (this.teams[this.currIndex]) {
                    if (this.teams[this.currIndex].is_checked !== true) {
                        this.currIndex++;
                        this.sendEmail();
                    } else {
                        const accessLink = window.location.protocol +
                                            '//' + window.location.host +
                                            '/quiz/' +
                                            this.eventId + ':' + this.teams[this.currIndex].access_key;

                        const loginLink = window.location.protocol +
                                            '//' + window.location.host;

                        this.api.send(
                            'events/send-access-to-teams',
                            {event_id: this.eventId},
                            {
                                team_id: this.teams[this.currIndex].id,
                                accessLink: accessLink,
                                loginLink: loginLink,
                            }
                        ).then(res => {
                            if (res['code'] === 200) {
                                this.teams[this.currIndex].status = 'OK';
                                this.teams[this.currIndex].is_ok = true;
                                this.teams[this.currIndex].is_fail = false;
                            } else {
                                this.teams[this.currIndex].status = 'ERROR';
                                this.teams[this.currIndex].is_ok = false;
                                this.teams[this.currIndex].is_fail = true;
                            }
                            this.currIndex++;
                            this.sendEmail();
                        }).catch(err => {
                            this.isProcess = false;
                            this.currIndex = 0;
                            this.woFlash.addError('Oops! Something went wrong!');
                            this.woFlash.show('sendEmail');
                        });
                    }
                }
            } else {
                this.woFlash.addMessage('The operation was done!');
                this.woFlash.show('sendEmail');
                this.isProcess = false;
                this.currIndex = 0;
            }
        }
    }

    doSelectAll() {
        this.teams.forEach(el => {
            el.is_checked = this.allChecked;
        });
    }
}
