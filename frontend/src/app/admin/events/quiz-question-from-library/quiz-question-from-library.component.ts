import {Component, AfterViewInit, Output, Input, EventEmitter} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {ApiAdminService} from '@app/share/api-admin.service';
import {WoFlashService} from '@app/wo-module/wo-flash/wo-flash.service';

@Component({
    selector: 'question-library-picker',
    templateUrl: 'quiz-question-from-library.component.html',
    styleUrls: ['quiz-question-from-library.component.css'],
})
export class QuizQuestionFromLibraryComponent implements AfterViewInit {
    @Output() add: EventEmitter<any> = new EventEmitter<any>();
    _quizzQuestions = [];
    @Input()
    set quizzQuestions(value: []) {
        this._quizzQuestions = value;
        this._updateRowsData();
    }

    criteria = {
        page: 0,
        defSort: '',
        sort: '',
        filterQuery: ['title', 'created_at_from', 'created_at_to', 'status', 'type', 'common'],
        preFilter: {},
        filter: {},
    };
    curr_page = 0;
    last_page = 0;
    advanced_filters = false;
    loading = true;

    rows = [];
    STATUS_LABELS = [
        'Active',
        'Blocked',
        'Deleted',
    ];

    TYPES = [
        'Multiple choice',
        'Checkboxes',
        'Dropdown',
        'Open answer',
    ];

    constructor(
        private router: Router,
        private woFlash: WoFlashService,
        private activeRoute: ActivatedRoute,
        private api: ApiAdminService
    ) {
    }

    ngAfterViewInit() {
        this.woFlash.show();
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

    toActualUrl() {
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

        this.router.navigate([this.router.url.split(';')[0], params]);
    }

    sortBy(event) {
        this.criteria.sort = event.current;
        this.toActualUrl();
    }

    doSearch() {
        this.criteria.filter = Object.assign({}, this.criteria.preFilter);
        this.toActualUrl();
    }

    toPage(event) {
        this.criteria.page = event['page'];
        this.toActualUrl();
    }

    toggleAdvancedFilter() {
        this.advanced_filters = !this.advanced_filters;
    }

    resetFilter(event: Event) {
        event.preventDefault();
        this.criteria.filter = {};
        this.toActualUrl();
    }

    _updateRowsData() {
        for (let i = 0; i < this.rows.length; i++) {
            this.rows[i]['alreadyIncluded'] = false;
            for (let j = 0; j < this._quizzQuestions.length; j++) {
                if (this._quizzQuestions[j].library_question_id == this.rows[i].id) {
                    this.rows[i]['alreadyIncluded'] = true;
                    break;
                }
                if (this._quizzQuestions[j].id == this.rows[i].library_created_from_id) {
                    this.rows[i]['alreadyIncluded'] = true;
                    break;
                }
            }
        }
    }

    findBy() {
        this.loading = true;
        const filter = this.criteria.filter;
        filter['status'] = 0;
        this.api.send('questions/index', this.criteria, {filter: filter}).then(res => {
            this.curr_page = res['curr_page'];
            this.last_page = res['last_page'];
            this.rows = res['rows'];
            this._updateRowsData();
            this.loading = false;
        });
    }

    addQuestion(row) {
        if (row['alreadyIncluded']) {
            return false;
        }
        this.loading = true;
        this.api.send('questions/item', {id: row['id']}).then(res => {
            if (res['data'] !== null) {
                this.loading = false;
                this.add.emit({
                    'question' : res['data'],
                    'answers' : res['answers'],
                });
            }
        });
    }

}
