import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '../wo-module/wo.module';
import { AdminRoutingModule } from './admin-routing.module';
import { LoginComponent } from './login/login.component';
import { AdminComponent } from './admin.component';
import { ShareModule } from './share/share.module';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        AdminRoutingModule,
        ShareModule
    ],
    declarations: [
        AdminComponent,
        LoginComponent,
    ]
})
export class AdminModule {}
