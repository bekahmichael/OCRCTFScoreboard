import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';
import { TeamTabsComponent } from '../team-tabs/team-tabs.component';

@Component({
    templateUrl: 'team-profile.component.html',
    styleUrls: ['team-profile.component.css']
})
export class TeamProfileComponent  implements OnInit {
    profileId: number;
    dataForm = {
        status: 0,
    };
    dataErrors = {};

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private woFlash: WoFlashService,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        public api: ApiAdminService,
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
            this.api.send('teams/profile', {id: this.profileId}).then(res => {
                if (res['data'] !== null) {
                    this.dataForm = res['data'];
                    TeamTabsComponent.title = res['data']['name'];
                }
            });
        }
    }

    doSave() {
        this.woFlash.hide();
        this.api.send('teams/profile', {id: this.profileId}, this.dataForm).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                if (this.profileId === 0) {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.router.navigate(['/admin/teams', res['data'].id]);
                    this.dataErrors = {};
                } else {
                    this.dataErrors = {};
                    setTimeout(() => {
                        this.woFlash.addMessage('The operation was successfully done!');
                        TeamTabsComponent.title = this.dataForm['name'];
                        this.woFlash.show();
                    }, 100);
                }
            }
        });
    }

    onAvatarChanged(event) {
        if (event.result && event.result.file.id) {
            this.dataForm['avatar_file_id'] = event.result.file.id;
        } else if (event.remove === true) {
            this.dataForm['avatar_file_id'] = null;
        }
    }
}
