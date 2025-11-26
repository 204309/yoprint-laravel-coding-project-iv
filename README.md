<h3>🌻 Operates the project through WSL Ubuntu from Window OS 👩🏻‍💻</h3>


<h2>Here are photos of the output: </h2>
<img width="1864" height="945" alt="スクリーンショット 2025-11-26 061145" src="https://github.com/user-attachments/assets/2ec4cbb2-b32b-4097-8b2d-0467da2e50ab" />
The image above shows the completed upload history table for both files: <i><b>yoprint_test_import.csv</b></i> and <i><b>yoprint_test_updated.csv</b></i>.
<br><br/>
<img width="1865" height="1001" alt="スクリーンショット 2025-11-26 061210" src="https://github.com/user-attachments/assets/8393a873-5e6e-461d-9ae6-2bec896e2fae" />
The image above shows the user dragging a file from their PC directory to upload it into the system.
<br><br/>
<img width="1868" height="1004" alt="スクリーンショット 2025-11-26 061219" src="https://github.com/user-attachments/assets/8b5eb182-4971-4bb6-aa7b-db281c31f279" />
The image above shows the uploaded file container along with the displayed file name after the user drops the file.
<br><br/>
<img width="1870" height="1006" alt="スクリーンショット 2025-11-26 061242" src="https://github.com/user-attachments/assets/a0efdd69-f7ab-48f6-91a5-e9628155de21" />
The image above shows a <b>notification bubble</b> that appears after the user successfully uploads a file for processing.
<br><br/>
<img width="1871" height="1003" alt="スクリーンショット 2025-11-26 061535" src="https://github.com/user-attachments/assets/30030a6d-c5e3-47fb-bbe4-11673ed0e269" />
The image above shows a list of uploaded files with their uploaded <b>Time, File Name,</b> and <b>Status</b>. The table can be sorted from newest to oldest by clicking the Time column header, and sorted alphabetically (ascending or descending) by clicking the File Name column header.
<br><br/>
<img width="1870" height="1006" alt="スクリーンショット 2025-11-26 061557" src="https://github.com/user-attachments/assets/3b4bd492-b2d4-4a50-8c64-3487295e5714" />
The yellow box under the Status column in the first row indicates that the record has just been updated through WebSockets (using Laravel Reverb) when the job queue finishes processing via Laravel Horizon.
<br><br/>
<img width="1870" height="1001" alt="スクリーンショット 2025-11-26 061946" src="https://github.com/user-attachments/assets/11290c42-89ac-415a-bd6b-eecaf26413e1" />
The image above shows how the Uploaded File History list looks when the status of an uploaded file is 'Processing'.

<br/><br/>
<h2>💾 SQLite Database Output: </h2>
<img width="1399" height="595" alt="スクリーンショット 2025-11-26 205332" src="https://github.com/user-attachments/assets/3806cddf-d999-4e3d-b66f-388b79621777" />
The image above shows the Uploaded File Histories table, where all basic information about uploaded files is stored. 
<br><br/>
<img width="1295" height="1076" alt="スクリーンショット 2025-11-26 205425" src="https://github.com/user-attachments/assets/c7e95937-339b-41d6-855e-309d1900a0f9" />
The image above shows the Products table, where all data processed and imported from the user's recently uploaded file is stored. 
