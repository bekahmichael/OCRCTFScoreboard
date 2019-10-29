import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';

@Component({
    templateUrl: 'scoreboard-template-profile.component.html',
    styleUrls: ['scoreboard-template-profile.component.css']
})
export class ScoreboardTemplateProfileComponent implements OnInit {
    profileId: number;
    dataErrors = {};
    dataForm = {};
    isLoading = false;

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
            this.metaPage.setTitle(this.profileId === 0 ? 'New Template' : 'Edit Template');
            this.woFlash.show();

            this.breadcrumbs.setLinks([
                {iconClass: 'fa fa-dashboard', route: '', text: 'Home'},
                {iconClass: 'fa fa-paint-brush', route: '/admin/scoreboards', text: 'Scoreboard Templates'},
                {text: 'Scoreboard Template Profile'},
            ]);

            this.loadData();
        });
    }

    loadData() {
        if (this.profileId > 0) {
            this.isLoading = true;
            this.api.send('scoreboard-templates/profile', {id: this.profileId}).then(res => {
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
        this.api.send('scoreboard-templates/profile', {id: this.profileId}, this.dataForm).then(res => {
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
                    this.router.navigate(['/admin/scoreboards', res['data'].id]);
                    this.dataErrors = {};
                } else {
                    this.dataErrors = {};
                    setTimeout(() => {
                        this.woFlash.addMessage('The operation was successfully done!');
                        this.woFlash.show();
                    }, 100);
                }
            }
        });
    }

    onImageChanged(event, column) {
        if (event.result && event.result.file.id) {
            this.dataForm[column] = event.result.file.id;
        } else if (event.remove === true) {
            this.dataForm[column] = null;
        }
    }
}
