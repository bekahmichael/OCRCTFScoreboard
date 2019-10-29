import { Component, Injectable, EventEmitter, Output, OnChanges, Input} from '@angular/core';

@Component({
    selector: 'wo-paginator',
    templateUrl: 'wo-paginator.component.html',
    styleUrls: ['wo-paginator.component.css'],
})

@Injectable()
export class WoPaginatorComponent implements OnChanges {
    public maxButtonCount = 9;
    public pagerList = [];
    @Input() current: number;
    @Input() last: number;
    @Output() changePage = new EventEmitter();

    constructor() {}

    ngOnChanges() {
        this.getPagerList();
    }

    getPagerList() {
        if (this.current) {
            this.pagerList = [];

            let beginPage = Math.max(0, Math.floor(this.current - this.maxButtonCount / 2));
            let endPage = beginPage + this.maxButtonCount - 1;

            if (endPage >= this.last) {
                    endPage = this.last - 1;
                    beginPage = Math.max(0, endPage * 1 - this.maxButtonCount * 1 + 1);
            }

            for (let index = beginPage; index <= endPage; index++) {
                this.pagerList.push({
                    num: index + 1,
                    isDisabled: false,
                    isActive: index + 1 === this.current * 1
                });
            }

            if (this.pagerList[0] && this.pagerList[0].num > 2) {
                this.pagerList[0] = {
                    num: 1,
                    isDisabled: false,
                    isActive: false
                };

                this.pagerList[1] = {
                    num: '...',
                    isActive: false,
                    isDisabled: true
                };
            }

            if (this.pagerList[this.pagerList.length - 1] && this.pagerList[this.pagerList.length - 1].num < this.last - 2) {
                this.pagerList[this.pagerList.length - 2] = {
                    num: '...',
                    isActive: false,
                    isDisabled: true
                };

                this.pagerList[this.pagerList.length - 1] = {
                    num: this.last,
                    isDisabled: false,
                    isActive: false
                };
            }

            return this.pagerList;
        }
    }

    next() {
        if (this.current < this.last) {
            this.setPage({num: this.current * 1 + 1, isDisabled: false});
        }
    }

    prev() {
        if (this.current > 1) {
            this.setPage({num: this.current * 1 - 1, isDisabled: false});
        }
    }

    setPage(p) {
        if (p.isDisabled === false) {
            this.changePage.emit({page: p.num});
        }
    }
}
