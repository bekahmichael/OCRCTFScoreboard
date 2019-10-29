import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule} from '@angular/router';

import { WoRadioComponent } from './wo-radio/wo-radio.component';
import { WoCheckboxComponent } from './wo-checkbox/wo-checkbox.component';
import { WoDatepickerComponent } from './wo-datepicker/wo-datepicker.component';
import { WoTimepickerComponent } from './wo-timepicker/wo-timepicker.component';
import { WoPreviewImageComponent } from './wo-preview-image/wo-preview-image.component';
import { WoColorpickerComponent } from './wo-colorpicker/wo-colorpicker.component';
import { WoSlideToggleComponent } from './wo-slide-toggle/wo-slide-toggle.component';
import { WoSlideToggleButtonComponent } from './wo-slide-toggle-button/wo-slide-toggle-button.component';

import { WoFlashService } from './wo-flash/wo-flash.service';
import { WoFlashComponent } from './wo-flash/wo-flash.component';

import { WoDatalistComponent } from './wo-datalist/wo-datalist.component';
import { WoAutocompleteComponent } from './wo-autocomplete/wo-autocomplete.component';

import { WoPaginatorComponent } from './wo-paginator/wo-paginator.component';
import { WoDeleteButtonComponent } from './wo-delete-button/wo-delete-button.component';
import { WoBreadcrumbsComponent } from './wo-breadcrumbs/wo-breadcrumbs.component';
import { WoBreadCrumbsService } from './wo-breadcrumbs/wo-breadcrumbs.service';

import { WoHighlightPipe } from './wo-highlight/wo-highlight.pipe';
import { WoSortComponent } from './wo-sort/wo-sort.component';

import { WoDialogService } from './wo-dialog/wo-dialog.service';
import { WoDialogsComponent } from './wo-dialog/wo-dialogs.component';
import { WoDialogComponent } from './wo-dialog/wo-dialog.component';
import { WoDialogDirective } from './wo-dialog/wo-dialog.directive';

import { WoDialogAlertComponent } from './wo-dialog/wo-dialog-alert/wo-dialog-alert.component';
import { WoDialogConfirmComponent } from './wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';
import { WoDialogPromptComponent } from './wo-dialog/wo-dialog-prompt/wo-dialog-prompt.component';

import { WoAvatarComponent } from './wo-avatar/wo-avatar.component';
import { WoAwatarEditComponent } from './wo-avatar/wo-awatar-edit.component';

const WO_COMPONENTS = [
    WoDialogComponent,
    WoDialogDirective,
    WoDialogsComponent,
    WoDialogAlertComponent,
    WoDialogConfirmComponent,
    WoDialogPromptComponent,
    WoRadioComponent,
    WoCheckboxComponent,
    WoDatepickerComponent,
    WoTimepickerComponent,
    WoPaginatorComponent,
    WoDatalistComponent,
    WoAutocompleteComponent,
    WoFlashComponent,
    WoHighlightPipe,
    WoSlideToggleComponent,
    WoSlideToggleButtonComponent,
    WoSortComponent,
    WoBreadcrumbsComponent,
    WoDeleteButtonComponent,
    WoAvatarComponent,
    WoAwatarEditComponent,
    WoColorpickerComponent,
    WoPreviewImageComponent,
];

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        RouterModule,
    ],

    declarations: WO_COMPONENTS,
    exports: WO_COMPONENTS,

    entryComponents: [
        WoDialogAlertComponent,
        WoDialogConfirmComponent,
        WoDialogPromptComponent,
        WoAwatarEditComponent,
    ],

    providers: [
        WoFlashService,
        WoDialogService,
        WoBreadCrumbsService,
    ]
})

export class WoModule {}
