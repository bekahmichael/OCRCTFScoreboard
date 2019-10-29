import {
    Component,
    Input,
    Output,
    EventEmitter
} from '@angular/core';

@Component({
  selector: 'wo-sort',
  templateUrl: './wo-sort.component.html',
  styleUrls: ['./wo-sort.component.css']
})
export class WoSortComponent {
    @Input() woSortActive;
    @Input() woSortStart;
    @Output() sort = new EventEmitter();

    get asc(): boolean {
        return this.column() === this.activeColumn() && this.woSortActive.indexOf('-') !== 0;
    }

    get desc(): boolean {
        return this.column() === this.activeColumn() && this.woSortActive.indexOf('-') === 0;
    }

    activeColumn() {
        return this.woSortActive.indexOf('-') === 0 ? this.woSortActive.substring(1) : this.woSortActive;
    }

    column() {
        return this.woSortStart.indexOf('-') === 0 ? this.woSortStart.substring(1) : this.woSortStart;
    }

    setSort() {
        this.sort.emit({
            current: (this.desc ? '' : '-') + this.column(),
        });
    }
}
