import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { AdminNewUserComponent } from '@app/modal/admin-new-user/admin-new-user.component';

@Component({
    templateUrl: 'team-participants.component.html',
    styleUrls: ['team-participants.component.css'],
})
export class TeamParticipantsComponent implements OnInit {
    id = 0;
    instance = this;
    criteria = {
        page: 0,
        defSort: 'username',
        sort: 'username',
        filterQuery: ['common', 'username', 'first_name', 'last_name', 'email', 'status', 'created_at'],
        preFilter: {},
        filter: {},
    };

    curr_page = 0;
    last_page = 0;
    advanced_filters = false;
    loading = true;

    rows = [];

    constructor (
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private metaPage: MetaPageService,
        private api: ApiAdminService,
        private dialog: WoDialogService,
    ) {
        this.metaPage.setTitle('Team Participants');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '', text: 'Home'},
            {text: 'Team Participants'},
        ]);
    }

    ngOnInit () {
        this.activeRoute.parent.params.subscribe(parentParams => {
            this.id = parseInt(parentParams['id'], 10) || 0;

            this.activeRoute.params.subscribe(params => {
                this.criteria['page'] = params['p'];
                this.criteria['sort'] = this.criteria.defSort;

                if (params['sort']) {
                    this.criteria.sort = params['sort'];
                }

                if (params['advanced'] === 'on') {
                    this.advanced_filters = true;
                }

                for (const prop in this.criteria.filterQuery) {
                    if (typeof params[this.criteria.filterQuery[prop]] !== 'undefined' && params[this.criteria.filterQuery[prop]] !== '') {
                        this.criteria.filter[this.criteria.filterQuery[prop]] = params[this.criteria.filterQuery[prop]];
                    }
                }

                this.criteria.preFilter = Object.assign({}, this.criteria.filter);
                this.findBy();
            });
        });
    }

    findBy() {
        this.loading = true;
        this.criteria['team_id'] = this.id;
        this.api.send('teams/participants', this.criteria, {filter: this.criteria.filter}).then(res => {
            this.curr_page = res['curr_page'];
            this.last_page = res['last_page'];
            this.rows = res['rows'];
            this.loading = false;
        });
    }

    toActualUrl () {
        const params = {};
        // Page
        if (this.criteria['page'] > 1) {
            params['p'] = this.criteria['page'];
        }

        // Sort
        if (this.criteria.sort !== this.criteria.defSort) {
            params['sort'] = this.criteria['sort'];
        }

        // filter
        for (const prop in this.criteria.filterQuery) {
            if (
                typeof this.criteria.filter[this.criteria.filterQuery[prop]] !== 'undefined' &&
                this.criteria.filter[this.criteria.filterQuery[prop]] !== ''
            ) {
                params[this.criteria.filterQuery[prop]] = this.criteria.filter[this.criteria.filterQuery[prop]];
            }
        }

        // Advanced filter
        if (this.advanced_filters === true) {
            params['advanced'] = 'on';
        }

        this.router.navigate(['/admin/teams', this.id, 'participants', params]);
    }

    sortBy(event) {
        this.criteria.sort = event.current;
        this.toActualUrl();
    }

    doSearch () {
        this.criteria.filter = Object.assign({}, this.criteria.preFilter);
        this.toActualUrl();
    }

    toProfile(id) {
        const dialogRef = this.dialog.open(AdminNewUserComponent, {id: id, role: 'public', hideRole: true});
        dialogRef.afterClosed().subscribe(result => {
            this.findBy();
        });
    }

    toPage(event) {
        this.criteria.page = event['page'];
        this.toActualUrl();
    }

    toggleAdvancedFilter () {
        this.advanced_filters = !this.advanced_filters;
    }

    resetFilter(event: Event) {
        event.preventDefault();
        this.criteria.filter = {};
        this.toActualUrl();
    }

    assignUser(row) {
        this.api.send('teams/assign-participant', {}, {
            team_id: this.id,
            user_id: parseInt(row.id, 10),
            is_assigned: row.is_assigned
        }).then(res => {});
    }
}
