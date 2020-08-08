import { Injectable, PipeTransform } from '@angular/core';

import { BehaviorSubject, Observable, of, Subject } from 'rxjs';

import { Country } from './country';
import { Upload } from './upload';
import { COUNTRIES } from './countries';
import { DecimalPipe } from '@angular/common';
import { debounceTime, delay, switchMap, tap } from 'rxjs/operators';
import { SortColumn, SortDirection } from './sortable.directive';
import { HttpClient, HttpHeaders } from '@angular/common/http';

interface SearchResult {
  uploads: Upload[];
  total: number;
}

interface State {
  page: number;
  pageSize: number;
  searchTerm: string;
  sortColumn: SortColumn;
  sortDirection: SortDirection;
}

const compare = (v1: string, v2: string) => v1 < v2 ? -1 : v1 > v2 ? 1 : 0;

function sort(uploads: Upload[], column: SortColumn, direction: string): Upload[] {
  if (direction === '' || column === '') {
    return uploads;
  } else {
    return [...uploads].sort((a, b) => {
      const res = compare(`${a[column]}`, `${b[column]}`);
      return direction === 'asc' ? res : -res;
    });
  }
}

function matches(upload: Upload, term: string) {
  return upload.customer_name.toLowerCase().includes(term.toLowerCase())
    || upload.email.toLowerCase().includes(term.toLowerCase())
    || upload.file_url.toLowerCase().includes(term.toLowerCase())
    || upload.project_type.toLowerCase().includes(term.toLowerCase())
    || upload.transaction_status.toLowerCase().includes(term.toLowerCase());
    // || pipe.transform(upload.transaction_id).includes(term)
    // || pipe.transform(upload.date_created).includes(term);
}

@Injectable({ providedIn: 'root' })
export class CountryService {
  private _loading$ = new BehaviorSubject<boolean>(true);
  private _search$ = new Subject<void>();
  private _uploads$ = new BehaviorSubject<Upload[]>([]);
  private _total$ = new BehaviorSubject<number>(0);

  private _state: State = {
    page: 1,
    pageSize: 6,
    searchTerm: '',
    sortColumn: '',
    sortDirection: ''
  };
  // SERVER_URL = "https://www.3dintegrationgroup.com/secure-file-upload/users.php";
  SERVER_URL = "http://localhost:80/users.php";
  UPLOADS: Upload[] = [];

  constructor(private pipe: DecimalPipe, private http: HttpClient) {
    this._search$.pipe(
      tap(() => this._loading$.next(true)),
      debounceTime(200),
      switchMap(() => this._search()),
      delay(200),
      tap(() => this._loading$.next(false))
    ).subscribe(result => {
      this._uploads$.next(result.uploads);
      this._total$.next(result.total);
    });

    this._search$.next();
  }

  get uploads$() { return this._uploads$.asObservable(); }
  get total$() { return this._total$.asObservable(); }
  get loading$() { return this._loading$.asObservable(); }
  get page() { return this._state.page; }
  get pageSize() { return this._state.pageSize; }
  get searchTerm() { return this._state.searchTerm; }

  set page(page: number) { this._set({ page }); }
  set pageSize(pageSize: number) { this._set({ pageSize }); }
  set searchTerm(searchTerm: string) { this._set({ searchTerm }); }
  set sortColumn(sortColumn: SortColumn) { this._set({ sortColumn }); }
  set sortDirection(sortDirection: SortDirection) { this._set({ sortDirection }); }

  private _set(patch: Partial<State>) {
    Object.assign(this._state, patch);
    this._search$.next();
  }

  private _search(): Observable<SearchResult> {
    const { sortColumn, sortDirection, pageSize, page, searchTerm } = this._state;

    const formData = new FormData();
    formData.append('action', 'getuploads');
    // Headers
    const headers = new HttpHeaders({
      // 'security-token': 'mytoken'
    });

    this.http.post(this.SERVER_URL, formData, { headers: null, responseType: 'json' })
      .subscribe(
        (res) => {
          console.log(res);
          console.log(JSON.stringify(res));
          // let resString = JSON.stringify(res);
          let resJson = JSON.parse(JSON.stringify(res));
          // console.log(resString.status);
          this.UPLOADS = resJson;
        },
        (err) => console.log(err)
      );

    // 1. sort
    let uploads = sort(this.UPLOADS, sortColumn, sortDirection);

    // 2. filter
    uploads = uploads.filter(upload => matches(upload, searchTerm));
    const total = uploads.length;

    // 3. paginate
    uploads = uploads.slice((page - 1) * pageSize, (page - 1) * pageSize + pageSize);
    return of({ uploads, total });
  }
}