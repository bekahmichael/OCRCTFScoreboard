import { Component, OnInit, Injectable, forwardRef, Input} from '@angular/core';
import { NG_VALUE_ACCESSOR, ControlValueAccessor } from '@angular/forms';

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    multi: true,
    /* tslint:disable */
    useExisting: forwardRef(() => WoCheckboxComponent)
    /* tslint:enable */
};

const noop = () => {};

@Component({
  selector: 'wo-checkbox',
  templateUrl: 'wo-checkbox.component.html',
  styleUrls: ['wo-checkbox.component.css'],
  providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoCheckboxComponent implements ControlValueAccessor, OnInit {
    // The internal data model
    public isChecked = false;
    @Input() checked: boolean;
    @Input() theme: string;
    @Input() trueValue: any = true;
    @Input() falseValue: any = false;

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    // From ControlValueAccessor interface
    writeValue(value: any) {
        if (value !== this.isChecked) {
            this.isChecked = value === this.trueValue;
            this.onChangeCallback(this.isChecked ? this.trueValue : this.falseValue);
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

    ngOnInit() {
        this.isChecked = !!this.checked;
    }

    onChange (event) {
        this.isChecked = !!event.target.checked;
        this.onChangeCallback(this.isChecked ? this.trueValue : this.falseValue);
    }
}
