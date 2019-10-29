import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '../wo-module/wo.module';
import { PublicRoutingModule } from './public-routing.module';
import { HomeComponent } from './home/home.component';
import { PublicComponent } from './public.component';
import { LoginComponent } from './login/login.component';
import { SignupComponent } from './signup/signup.component';
import { RequestPasswordResetComponent } from './request-password-reset/request-password-reset.component';
import { ConfirmRegistrationComponent } from './confirm-registration/confirm-registration.component';
import { SuccessRegistrationComponent } from './success-registration/success-registration.component';
import { PasswordResetComponent } from './password-reset/password-reset.component';
import { ShareModule } from './share/share.module';
import { MyProfileComponent } from './my-profile/my-profile.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        PublicRoutingModule,
        ShareModule,
    ],
    declarations: [
        PublicComponent,
        HomeComponent,
        LoginComponent,
        SignupComponent,
        RequestPasswordResetComponent,
        ConfirmRegistrationComponent,
        SuccessRegistrationComponent,
        PasswordResetComponent,
        MyProfileComponent,
    ]
})
export class PublicModule {}
