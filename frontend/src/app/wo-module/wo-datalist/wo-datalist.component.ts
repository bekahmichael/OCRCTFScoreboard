import { Component, AfterViewInit, Injectable, forwardRef, ElementRef, ViewChild, Input } from '@angular/core';
import { NG_VALUE_ACCESSOR, ControlValueAccessor } from '@angular/forms';

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    multi: true,
    /* tslint:disable */
    useExisting: forwardRef(() => WoDatalistComponent),
    /* tslint:enable */
};

const noop = () => {};

@Component({
  selector: 'wo-datalist',
  templateUrl: 'wo-datalist.component.html',
  styleUrls: ['wo-datalist.component.css'],
  providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoDatalistComponent implements ControlValueAccessor, AfterViewInit {
    @ViewChild('woDetilsItems', {static: false}) woDetilsItems: ElementRef;
    @Input() value;
    @Input() placeholder: any = '';

    // The internal data model
    public isChecked = false;
    public checked: boolean;

    // The internal data model
    public innerValue: any = '';
    public inputText: any = '';

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    ngAfterViewInit() {
        this.getOptions().forEach((item) => {
            item.addEventListener('mousedown', (event) => { this.doSelect(event); });
        });
    }

    getOptions () {
        return this.woDetilsItems.nativeElement.querySelectorAll('[value]');
    }

    // From ControlValueAccessor interface
    writeValue(value: any) {
        if (value !== this.innerValue) {
            this.innerValue = value;
            this.updateInputValue();
            this.onChangeCallback(this.innerValue);
        }
    }

    // From ControlValueAccessor interface
    registerOnChange(fn: any) {
        this.onChangeCallback = fn;
    }

    // From ControlValueAccessor interface
    registerOnTouched(fn: any) {
        this.onTouchedCallback = fn;
    }

    onChange (event) {
        this.onChangeCallback(this.innerValue);
    }

    doSelect (event) {
        this.removeCheckedElement();
        event.target.classList.add('wo-datalist_checked');
        this.writeValue(event.target.getAttribute('value'));
        this.updateInputValue();
    }

    updateInputValue() {
        const options = this.getOptions();
        const length = options.length;

        for (let i = 0; i < length; i++) {
            if (options[i].getAttribute('value') === this.innerValue) {
                this.inputText = options[i].innerText;
                break;
            }
        }
    }

    removeCheckedElement() {
        const el = this.woDetilsItems.nativeElement.querySelector('[value].wo-datalist_checked');
        if (el) {
            el.classList.remove('wo-datalist_checked');
        }
    }

    onBlur(event) {
        const inputText = this.inputText.trim().toLowerCase();

        const options = this.getOptions();
        const length = options.length;
        let isFind = false;

        this.removeCheckedElement();

        for (let i = 0; i < length; i++) {
            if (options[i].innerText.toLowerCase() === inputText) {
                this.inputText = options[i].innerText;
                options[i].classList.add('wo-datalist_checked');
                isFind = true;

                this.writeValue(options[i].getAttribute('value'));
                break;
            }
        }

        if (isFind === false) {
            this.inputText = '';
            this.writeValue(null);
        }
    }

    doFilterValues() {
        this.woDetilsItems.nativeElement.querySelectorAll('[value].wo-datalist_hide').forEach(hideEl => {
            hideEl.classList.remove('wo-datalist_hide');
        });

        const searchText = this.inputText.trim().toLowerCase();

        if (this.inputText !== '') {
            this.getOptions().forEach(el => {
                if (el.innerText.toLowerCase().indexOf(searchText) === -1) {
                    el.classList.add('wo-datalist_hide');
                }
            });
        }
    }

    doShow () {
        this.doFilterValues();
    }
}
