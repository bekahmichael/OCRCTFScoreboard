import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';


import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogConfirmComponent } from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';

@Component({
    templateUrl: 'scoreboards-template-list.component.html',
    styleUrls: ['scoreboards-template-list.component.css'],
})
export class ScoreboardTemplateListComponent implements OnInit {
    instance = this;
    criteria = {
        page: 0,
        defSort: 'name',
        sort: 'name',
        filterQuery: ['common', 'name'],
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
        this.metaPage.setTitle('Scoreboard Templates');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-dashboard', route: '', text: 'Home'},
            {text: 'Scoreboard Templates'},
        ]);
    }

    ngOnInit () {
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
    }

    findBy() {
        this.loading = true;
        this.api.send('scoreboard-templates/index', this.criteria, {filter: this.criteria.filter}).then(res => {
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
        if (this.criteria.sort != this.criteria.defSort) {
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

        this.router.navigate(['/admin/scoreboards', params]);
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
        this.router.navigate(['/admin/scoreboards', id]);
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

    doDelete(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete template?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('scoreboard-templates/delete', {id: row['id']}, {}).then(res => {
                    this.findBy();
                });
            }
        });
    }
}
