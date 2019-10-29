import { BrowserModule, Title } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule, Routes } from '@angular/router';
import { CoreModule } from './core/core.module';
import { WoModule } from './wo-module/wo.module';
import { ShareModule } from './share/share.module';
import { ModalModule } from '@app/modal/modal.module';

import { AppComponent } from './app.component';
import { PageNotFoundComponent } from './share/page-not-found/page-not-found.component';

import { AuthService } from './auth/auth.service';
import { AuthAdminGuard } from './auth/auth-admin-guard.service';
import { AuthGuestGuard } from './auth/auth-guest-guard.service';

const appRoutes: Routes = [
  { path: 'admin', loadChildren: './admin/admin.module#AdminModule'},
  // { path: '', loadChildren: './public/public.module#PublicModule'},
  { path: 'scoreboard', loadChildren: './scoreboard/scoreboard.module#ScoreboardModule'},
  { path: 'quiz', loadChildren: './quiz/quiz.module#QuizModule'},
  // { path: '', redirectTo: '/admin/users', pathMatch: 'full' },
  { path: '', redirectTo: '/quiz/login', pathMatch: 'full' },
  { path: '**', component: PageNotFoundComponent }
];

@NgModule({
  declarations: [
    AppComponent,
    PageNotFoundComponent,
  ],

  entryComponents: [
  ],

  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    FormsModule,
    HttpClientModule,
    RouterModule.forRoot(appRoutes),
    CoreModule,
    WoModule,
    ModalModule,
    ShareModule,
  ],

  providers: [
    AuthAdminGuard,
    AuthGuestGuard,
    AuthService,
    Title,
  ],

  bootstrap: [AppComponent]
})

export class AppModule {}
