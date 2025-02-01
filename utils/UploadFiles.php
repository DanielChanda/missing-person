<?php 
class UploadFiles{
    /**
     * handles the uploading of files on the save
     * @param string the path where the file will be uploaded
     * @param array an array of allowed extensions
     * @param int the maxmum file size to accomodate
     * @param string the unique name of the file
     * @param string where to redirect to if un error occurs
     * @return string the full file path
     */
    public function upload($upload_dir, $allowed_extensions, $maxFileSize, $unique_name, $goTo){

        // Handle file upload

        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        if (in_array($file_extension, $allowed_extensions) && $_FILES['image']['size'] <= $maxFileSize) {
            
            $image_path = $upload_dir . $unique_name . '.' . $file_extension;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            title: 'Upload failure',
                            text: 'Failed to upload image.',
                            icon: 'error'
                        }).then(() => {
                            window.location.href = "<?php echo $goTo;?>";
                        });
                    });
                </script>
                <?php 
                exit();
            }
        } else {
            ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'Validation error',
                        text: 'Invalid file type or size.',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = "<?php echo $goTo;?>";
                    });
                });
            </script>
            <?php 
            exit();
        }

        return $image_path;
               
    }
}

?>