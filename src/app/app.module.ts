import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { APP_BASE_HREF } from '@angular/common';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HttpClientModule } from '@angular/common/http';

import { AppComponent } from './app.component';
import { MustMatchDirective } from './_helpers/must-match.directive';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ViewUploadsComponent } from './view-uploads/view-uploads.component';



@NgModule({
  declarations: [
    AppComponent,
    MustMatchDirective,
    ViewUploadsComponent,
  ],
  imports: [
    BrowserModule,
    NgbModule,
    ReactiveFormsModule,
    FormsModule,
    HttpClientModule,
  ],
  providers: [
    { provide: APP_BASE_HREF, useValue: '/secure-file-upload/' },
  ],
  bootstrap: [AppComponent],
  entryComponents: [],
  exports: []
})
export class AppModule { }
