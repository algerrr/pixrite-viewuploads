import { DecimalPipe } from '@angular/common';
import { Component, QueryList, ViewChildren, OnInit, OnDestroy } from '@angular/core';
import { Observable } from 'rxjs';

import { Country } from './country';
import { CountryService } from './country.service';
import { NgbdSortableHeader, SortEvent } from './sortable.directive';
import { Upload } from './upload';

@Component(
  {
    selector: 'app-view-uploads',
    templateUrl: './view-uploads.component.html',
    providers: [CountryService, DecimalPipe]
  })
export class ViewUploadsComponent implements OnInit {
  uploads$: Observable<Upload[]>;
  total$: Observable<number>;


  @ViewChildren(NgbdSortableHeader) headers: QueryList<NgbdSortableHeader>;

  constructor(public service: CountryService) {
    this.uploads$ = service.uploads$;
    this.total$ = service.total$;

  }

  ngOnInit(): void {
  }

  onSort({ column, direction }: SortEvent) {
    // resetting other headers
    this.headers.forEach(header => {
      if (header.sortable !== column) {
        header.direction = '';
      }
    });

    this.service.sortColumn = column;
    this.service.sortDirection = direction;
  }
}