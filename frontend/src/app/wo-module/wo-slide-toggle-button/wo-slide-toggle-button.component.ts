import { Component, Injectable, Input, forwardRef} from '@angular/core';
import { NG_VALUE_ACCESSOR, ControlValueAccessor } from '@angular/forms';

const noop = () => {};

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    multi: true,
    /* tslint:disable */
    useExisting: forwardRef(() => WoSlideToggleButtonComponent)
    /* tslint:enable */
};

@Component({
  selector: 'wo-slide-toggle-button',
  templateUrl: 'wo-slide-toggle-button.component.html',
  styleUrls: ['wo-slide-toggle-button.component.css'],
  providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoSlideToggleButtonComponent implements ControlValueAccessor {
    // The internal data model
    public isChecked = false;
    @Input() checked: boolean;
    @Input() trueValue: any = true;
    @Input() falseValue: any = false;
    @Input() disabled: any = false;
    @Input() textON: any = 'ON';
    @Input() textOFF: any = 'OFF';

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    // From ControlValueAccessor interface
    writeValue(value: any) {
        if (value !== null) {
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

    onToggle() {
        if (this.disabled === false) {
            this.isChecked = !this.isChecked;
            this.onChangeCallback(this.isChecked ? this.trueValue : this.falseValue);
        }
    }
}
