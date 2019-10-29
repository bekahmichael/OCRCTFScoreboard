import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CoreModule } from '@app/core/core.module';
import { RouterModule } from '@angular/router';

// Other Components
import { UserMenuComponent } from './user-menu/user-menu.component';

@NgModule({
    declarations: [
        UserMenuComponent,
    ],
    imports: [
        CommonModule,
        CoreModule,
        RouterModule,
    ],
    exports: [
        UserMenuComponent,
    ],
})
export class ShareModule {}
