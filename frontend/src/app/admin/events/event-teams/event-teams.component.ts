import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';
import { environment } from '../../../../environments/environment';


import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { AdminAccessToTeamsComponent } from '@app/modal/admin-access-to-teams/admin-access-to-teams.component';
import { AuthService } from '@app/auth/auth.service';


@Component({
    templateUrl: 'event-teams.component.html',
    styleUrls: ['event-teams.component.css'],
})
export class EventTeamsComponent implements OnInit {
    instance = this;
    id = 0;
    criteria = {
        page: 0,
        defSort: 'name',
        sort: 'name',
        filterQuery: ['common', 'name', 'assigned'],
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
        private auth: AuthService,
    ) {
        this.metaPage.setTitle('Teams List');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '', text: 'Home'},
            {text: 'Teams List'},
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
        this.criteria['id'] = this.id;
        this.api.send('events/teams', this.criteria, {filter: this.criteria.filter}).then(res => {
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

        this.router.navigate(['/admin/events/', this.id, 'teams', params]);
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
        //
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

    assignTeam(row) {
        this.api.send('events/assign-team', {}, {
            event_id: this.id,
            team_id: parseInt(row.id, 10),
            is_assigned: row.is_assigned
        }).then(res => {
            row['access_key'] = res['record']['access_key'];
            row['pin'] = res['record']['pin'].toUpperCase();
        });
    }

    copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.textContent = text;
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }

    copyAccessLink(row) {
        if (row.is_assigned === true) {
            this.copyToClipboard(
                window.location.protocol + '//' + window.location.host + '/quiz/' + row.id + ':' + row.access_key
            );
        }
    }

    sendAccessCodesByEmail() {
        const dialogRef = this.dialog.open(AdminAccessToTeamsComponent, {eventId: this.id});
        dialogRef.afterClosed().subscribe(result => {
        });
    }

    doDownloadAccessPINs() {
        window.location.href = environment.API_BASE_URL +
            '/admin/events/download-access-pins?event_id=' + this.id +
            '&access-token=' + this.auth.getToken();
    }
}
