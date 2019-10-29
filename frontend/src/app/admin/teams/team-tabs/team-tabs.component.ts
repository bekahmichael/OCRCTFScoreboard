import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

@Component({
    templateUrl: 'team-tabs.component.html',
    styleUrls: ['team-tabs.component.css']
})
export class TeamTabsComponent implements OnInit {
    static title = '';

    id = 0;

    constructor (
        private metaPage: MetaPageService,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiAdminService,
    ) {
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {iconClass: 'fa fa-users', route: '/admin/teams', text: 'Teams List'},
            {text: 'Team Profile'},
        ]);
    }

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.id = parseInt(params['id'], 10) || 0;
            if (this.id > 0) {
                this.loadData();
            } else {
                TeamTabsComponent.title = 'New Team';
            }
        });
    }

    loadData() {
        this.api.send('teams/profile', {id: this.id}).then(res => {
            TeamTabsComponent.title = res['data']['name'];
        });
    }

    get staticTitle() {
        return TeamTabsComponent.title;
    }
}
