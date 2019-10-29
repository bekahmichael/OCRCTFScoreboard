import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';
import { EventTabsComponent } from '../event-tabs/event-tabs.component';

@Component({
    templateUrl: 'event-profile.component.html',
    styleUrls: ['event-profile.component.css']
})
export class EventProfileComponent  implements OnInit {
    profileId: number;
    dataForm = {
        status: 0,
        event_time_start: '12:00',
        event_time_end: '15:00',
        duration: 180,
    };
    dataErrors = {};

    isLoading = false;

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private woFlash: WoFlashService,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiAdminService,
    ) {}

    ngOnInit () {
        this.activeRoute.params.subscribe(params => {
            this.profileId = Number(params['id']) || 0;
            this.metaPage.setTitle(this.profileId === 0 ? 'New' : 'Edit');
            this.woFlash.show();
            this.loadData();
        });
    }

    loadData() {
        if (this.profileId > 0) {
            this.isLoading = true;
            this.api.send('events/profile', {id: this.profileId}).then(res => {
                this.isLoading = false;
                if (res['data'] !== null) {
                    this.dataForm = res['data'];
                }
            });
        }
    }

    doSave() {
        this.woFlash.hide();
        this.isLoading = true;
        this.api.send('events/profile', {id: this.profileId}, this.dataForm).then(res => {
            this.isLoading = false;
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                if (this.profileId === 0) {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.router.navigate(['/admin/events', res['data'].id]);
                    this.dataErrors = {};
                } else {
                    this.dataErrors = {};
                    setTimeout(() => {
                        EventTabsComponent.title = this.dataForm['name'];
                        this.woFlash.addMessage('The operation was successfully done!');
                        this.woFlash.show();
                    }, 100);
                }
            }
        });
    }
}
