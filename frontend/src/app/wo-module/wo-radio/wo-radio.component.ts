import { Component , Input, forwardRef} from '@angular/core';
import { NG_VALUE_ACCESSOR } from '@angular/forms';

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    multi: true,
    /* tslint:disable */
    useExisting: forwardRef(() => WoRadioComponent)
    /* tslint:enable */
};

const noop = () => {};

@Component({
    selector: 'wo-radio',
    templateUrl: 'wo-radio.component.html',
    styleUrls: ['wo-radio.component.css'],
    providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})
export class WoRadioComponent {
    @Input() value: string;
    @Input() name: string;
    public checked: boolean;

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    // From ControlValueAccessor interface
    writeValue (value: any) {
        if (this.value === value) {
            this.checked = true;
        } else {
            this.checked = false;
        }
        if (this.checked === true) {
            this.onChangeCallback(this.value);
        }
    }

    // From ControlValueAccessor interface
    registerOnChange (fn: any) {
        this.onChangeCallback = fn;
    }

    // From ControlValueAccessor interface
    registerOnTouched (fn: any) {
        this.onTouchedCallback = fn;
    }

    onChange (event) {
        this.checked = true;
        this.onChangeCallback(this.value);
    }
}
