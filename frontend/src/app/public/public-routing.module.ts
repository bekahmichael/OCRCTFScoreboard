import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuestGuard } from '../auth/auth-guest-guard.service';
import { AuthPublicGuard } from '../auth/auth-public-guard.service';
import { HomeComponent } from './home/home.component';
import { PublicComponent } from './public.component';
import { LoginComponent } from './login/login.component';
import { SignupComponent } from './signup/signup.component';
import { RequestPasswordResetComponent } from './request-password-reset/request-password-reset.component';
import { ConfirmRegistrationComponent } from './confirm-registration/confirm-registration.component';
import { SuccessRegistrationComponent } from './success-registration/success-registration.component';
import { PasswordResetComponent } from './password-reset/password-reset.component';
import { MyProfileComponent } from './my-profile/my-profile.component';

const routes: Routes = [
    {
        path: '', component: PublicComponent ,
        children: [
            { path: '', component: HomeComponent },
            // { path: 'login', canActivate: [AuthGuestGuard], component: LoginComponent },
            // { path: 'request-password-reset', canActivate: [AuthGuestGuard], component: RequestPasswordResetComponent },
            // { path: 'signup', canActivate: [AuthGuestGuard], component: SignupComponent },
            // { path: 'password-reset/:token', canActivate: [AuthGuestGuard], component: PasswordResetComponent },
            // { path: 'confirm-registration/:token', component: ConfirmRegistrationComponent },

            // { path: 'success-registration', canActivate: [AuthPublicGuard], component: SuccessRegistrationComponent },
            // { path: 'my-profile', component: MyProfileComponent },
        ],
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class PublicRoutingModule {}
