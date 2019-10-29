import {
    Component,
    ElementRef,
    HostListener,
    forwardRef,
    Injectable,
    ViewChild,
    Input,
} from '@angular/core';
import { NG_VALUE_ACCESSOR, ControlValueAccessor } from '@angular/forms';

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    useExisting: forwardRef(() => WoColorpickerComponent),
    multi: true
};

const noop = () => {};

@Component({
    selector: 'wo-colorpicker',
    templateUrl: 'wo-colorpicker.component.html',
    styleUrls: ['wo-colorpicker.component.css'],
    providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoColorpickerComponent implements ControlValueAccessor {
    @ViewChild('wrapper', {static: false}) wrapperEl: ElementRef;

    @Input() mode = 'rgb';

    public currentColor = null;
    public isOpen = false;

    opacity = 1;
    greyColor = [
        [0, 0, 0],
        [68, 68, 68],
        [102, 102, 102],
        [153, 153, 153],
        [204, 204, 204],
        [238, 238, 238],
        [255, 255, 255],
    ];

    standartColor = [
        [255, 0, 0],
        [255, 153, 0],
        [255, 255, 0],
        [0, 255, 0],
        [0, 255, 255],
        [0, 0, 255],
        [153, 0, 255],
        [255, 0, 255],
    ];

    themeColor = [
        [
            [244, 204, 204],
            [252, 229, 205],
            [255, 242, 204],
            [217, 234, 211],
            [208, 224, 227],
            [207, 226, 243],
            [217, 210, 233],
            [234, 209, 220],
        ],
        [
            [234, 153, 153],
            [249, 203, 156],
            [255, 229, 153],
            [182, 215, 168],
            [162, 196, 201],
            [159, 197, 232],
            [180, 167, 214],
            [213, 166, 189],
        ],
        [
            [224, 102, 102],
            [246, 178, 107],
            [255, 217, 102],
            [147, 196, 125],
            [118, 165, 175],
            [118, 165, 175],
            [142, 124, 195],
            [194, 123, 160],
        ],
        [
            [204, 0, 0],
            [230, 145, 56],
            [241, 194, 50],
            [106, 168, 79],
            [69, 129, 142],
            [61, 133, 198],
            [103, 78, 167],
            [166, 77, 121],
        ],
        [
            [153, 0, 0],
            [180, 95, 6],
            [191, 144, 0],
            [56, 118, 29],
            [19, 79, 92],
            [11, 83, 148],
            [53, 28, 117],
            [116, 27, 71],
        ],
        [
            [102, 0, 0],
            [120, 63, 4],
            [127, 96, 0],
            [39, 78, 19],
            [12, 52, 61],
            [7, 55, 99],
            [32, 18, 77],
            [76, 17, 48],
        ],
    ];

    // The internal data model
    public innerValue: any = '';


    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    @HostListener('window:mousedown', ['$event']) clickOnWindow(event: KeyboardEvent) {
        if (this.wrapperEl.nativeElement.contains(event.target) === false) {
            this.isOpen = false;
        }
    }

    // get accessor
    get value(): any {
        return this.innerValue;
    }

    // set accessor including call the onchange callback
    set value(v: any) {
        if (v !== this.innerValue) {
            this.innerValue = v;
            this.onChangeCallback(v);
        }
    }

    // From ControlValueAccessor interface
    writeValue(value: any) {
        if (value !== this.innerValue) {
            this.parseValue(value);
            this.innerValue = value;
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

    open() {
        this.isOpen = true;
    }

    parseValue(value) {
        if (value) {
            if (this.mode === 'rgba') {
                const parseValues = value.substring(5, value.length - 1).replace(/ /g, '').split(',');
                this.currentColor = parseValues.slice(0, 3);
                this.opacity = parseValues[3];
            } else {
                this.currentColor = value.substring(4, value.length - 1).replace(/ /g, '').split(',');
            }

            for (let i = 0; i < this.currentColor.length; i++) {
                this.currentColor[i] = +this.currentColor[i];
            }

            if (this.currentColor.currentColor < 3) {
                this.currentColor = null;
            }
        }

        this.updateValue();
    }

    updateValue() {
        if (this.currentColor === null) {
            this.value = '';
        } else {
            if (this.mode === 'rgba') {
                this.value = 'rgba(' + this.currentColor.join(', ') + ', ' + this.opacity + ')';
            } else {
                this.value = 'rgb(' + this.currentColor.join(', ') + ')';
            }
        }
    }

    setColor(rgb) {
        this.currentColor = rgb;
        this.updateValue();
        this.isOpen = false;
    }
}
