import { Component, OnInit } from '@angular/core';
import { NgxFileDropEntry, FileSystemFileEntry, FileSystemDirectoryEntry } from 'ngx-file-drop';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { FormBuilder, NgForm } from '@angular/forms';


@Component({
  selector: 'app-file-upload',
  templateUrl: './file-upload.component.html',
  styleUrls: ['./file-upload.component.less'],
  providers: []
})
export class FileUploadComponent implements OnInit {
  title = 'pixrite';
  // SERVER_URL = "https://www.3dintegrationgroup.com/secure-file-upload/upload.php";
  SERVER_URL = "http://localhost:80/upload.php";

  public files: NgxFileDropEntry[] = [];
  model: any = {};
  uploadForm: NgForm;
  submitMode: boolean = true;
  showUploadTable: boolean = false;
  captchaResolved: boolean = false;
  
  txnId = "";
  txnStatus = "";

  constructor(private formBuilder: FormBuilder, private http: HttpClient) {
   }

  ngOnInit() {
    // this.uploadForm = this.formBuilder.group({
    //   fileToUpload: ['']
    // });
    // this.model.projectType = '3dPrinting';
    // this.model.customerName = 'Alger Brigham';
    // this.model.company = 'Cayuse Technologies';
    // this.model.zipcode = '97801';
    // this.model.email = 'alger.brigham@gmail.com';
    // this.model.phone = '5413107377';
    // this.model.notes = 'FAKE NOTES';
  }

  onSubmit() {
    console.log(this.model.customerName);
    // this.fileService.confirmUpload(this.model.customerName);
    
    for (const droppedFile of this.model.filesToUpload) {

      // Is it a file?
      if (droppedFile.fileEntry.isFile) {
        const fileEntry = droppedFile.fileEntry as FileSystemFileEntry;
        fileEntry.file((file: File) => {

          // console.log(droppedFile.relativePath, file);
          // this.uploadForm.get('fileToUpload').setValue(file);
          
          const formData = new FormData()
          formData.append('fileToUpload', file, droppedFile.relativePath)
          // formData.append('fileToUpload', this.uploadForm.get('fileToUpload').value);
          formData.append('projectType', this.model.projectType)
          formData.append('customerName', this.model.customerName)
          formData.append('zipcode', this.model.zipcode)
          formData.append('email', this.model.email)
          formData.append('phone', this.model.phone)
          formData.append('company', this.model.company)
          formData.append('notes', this.model.notes)
 
          // Headers
          const headers = new HttpHeaders({
            // 'security-token': 'mytoken'
          });
 
          this.http.post(this.SERVER_URL, formData, { headers: headers, responseType: 'json' })
          .subscribe(
            (res) => {
              console.log(res);
              let resJson = JSON.parse(JSON.stringify(res));
              this.txnId = resJson.txnId;
              this.txnStatus = resJson.txnStatus;
            },
            (err) => console.log(err)
          );
          // console.log('SUCCESS!! :-)\n\n' + JSON.stringify(this.model, null, 3));
          
        });
      } else {
        // It was a directory (empty directories are added, otherwise only files)
        const fileEntry = droppedFile.fileEntry as FileSystemDirectoryEntry;
        console.log(droppedFile.relativePath, fileEntry);
      }
      this.submitMode = false;
    }
  }

  public dropped(files: NgxFileDropEntry[]) {
    this.files = files;
    this.model.filesToUpload = files;
    this.showUploadTable = true;
  }

  public fileOver(event) {
    console.log(event);
  }

  public fileLeave(event) {
    console.log(event);
  }

  resolved(captchaResponse: string, res) {
    console.log(`Resolved response token: ${captchaResponse}`);
    this.captchaResolved = true;
  }

  toggleConfirm(){
    if(this.submitMode)
      this.submitMode = false;
    else
      this.submitMode = true;

    this.model = {};
    this.files = []
    this.txnId = "";
    this.txnStatus = "";
    this.showUploadTable = false;
    this.captchaResolved = false;
    // this.uploadForm.reset();

  }
}
