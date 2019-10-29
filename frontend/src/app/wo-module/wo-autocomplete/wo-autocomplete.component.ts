/**
 * @desc The wo-autocomplete component is a wrapper for a normal text input enhanced by a panel of suggested options.
 * @author Pavel Shklyarik
 */
import {
    Component,
    ElementRef,
    Renderer2,
    Input,
    Output,
    EventEmitter,
    AfterViewInit,
    HostListener
} from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Subject } from 'rxjs/Subject';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/debounceTime';
import 'rxjs/add/operator/distinctUntilChanged';
import 'rxjs/add/operator/switchMap';

@Component({
    selector: 'wo-autocomplete',
    templateUrl: 'wo-autocomplete.component.html',
    styleUrls: ['wo-autocomplete.component.css'],
})
export class WoAutocompleteComponent implements AfterViewInit {
    @Input() url: string;
    @Input() valueName: string;
    @Output() choose = new EventEmitter();

    curVal = '';
    showResult = false;
    isLoading = false;
    items = [];
    searchInput: HTMLInputElement;
    selectedItem = -1;
    changed$ = new Subject<any>();

    /**
     * Constructor.
     */
    constructor (
        private rootElement: ElementRef,
        private renderer: Renderer2,
        private http: HttpClient
    ) {
        this.changed$.debounceTime(50).distinctUntilChanged().switchMap(term => this.search(term)).subscribe((result) => {
            // @ts-ignore
            this.items = result;
            this.isLoading = false;
        });
    }

    /**
     * Close block with result if click outside of the component.
     * @param event the object.
     */
    @HostListener('document:click') clickOnDocument (event) {
        if (!this.rootElement.nativeElement.contains(event.target)) {
            this.showResult = false;
        }
    }

    /**
     * Callback called when key has been press.
     * @param event the object.
     */
    @HostListener('document:keydown') keyDownOnDocument (event) {
        if (this.showResult === true) {
            if (event.keyCode === 40 && this.selectedItem < this.items.length - 1) {
                this.selectedItem++;
                event.preventDefault();
            } else if (event.keyCode === 38 && this.selectedItem >= 0) {
                this.selectedItem--;
                event.preventDefault();
            } else if (event.keyCode === 13) {
                if (this.selectedItem > -1) {
                    this.setValue(this.items[this.selectedItem]);
                    event.preventDefault();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    ngAfterViewInit() {
        this.searchInput = this.rootElement.nativeElement.querySelector('input[type=text]');

        this.searchInput.autocomplete = 'off';

        this.renderer.listen(this.searchInput, 'focus',   ($event) => { this.onFocus($event); });
        this.renderer.listen(this.searchInput, 'keydown', ($event) => { this.onKeyDown($event); });
        this.renderer.listen(this.searchInput, 'input',   ($event) => { this.onInputChange($event); });
    }

    /**
     * Callback called when autocomplete input has been focused.
     * @param event the object in the DOM are based on the Event Object.
     */
    onFocus(event) {
        this.showResult = true;
    }

    /**
     * Callback called when key has been press inside input search.
     * @param event the object in the DOM are based on the Event Object.
     */
    onKeyDown (event) {
        if (event.keyCode === 13 && this.selectedItem === -1) {
            this.items = [];
            this.showResult = false;
        } else {
            this.showResult = true;
        }
    }

    /**
     * Callback called when value changes.
     * @param event the object in the DOM are based on the Event Object.
     */
    onInputChange(event) {
        this.curVal = event.target.value;
        if (this.curVal === '') {
            this.items = [];
        } else {
            this.isLoading = true;
            this.changed$.next(this.curVal);
        }
    }

    /**
     * Do request for find items.
     * @param term the search term.
     */
    search(term: string) {
        return this.http.post(this.url, {'s': term}).map(response => response);
    }

    /**
     * Sets the autocomplete's value.
     * @param item the item from autocomplete
     */
    setValue (item) {
        this.showResult = false;
        this.items = [];
        this.selectedItem = -1;
        this.choose.emit(item);
    }
}
