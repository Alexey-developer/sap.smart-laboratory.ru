import { useEffect } from 'react'

import ReactResumableJs from 'react-resumable-js/src/ReactResumableJs'

import constants from '@utils/constants.json'

export const ResumableUploadModel: React.FC = () => {
  useEffect(() => {
    // const r = new ReactResumableJs()
    // console.log(ReactResumableJs.state)
    // console.log(r.state)

    const btnStart = document.getElementsByClassName('btn start')
    btnStart[0].classList.add('btn-success')
    btnStart[0].classList.add('mt-1')
    btnStart[0].innerHTML =
      '<i class="fa-solid fa-cloud-arrow-up"></i> Начать загрузку'
    btnStart[0].setAttribute('disabled', 'true')
  }, [])

  return (
    <ReactResumableJs
      headerObject={{
        Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
      }}
      maxFileSize={512000000}
      uploaderID='image-upload'
      dropTargetID='myDropTarget'
      filetypes={['h5']}
      // fileAccept='image/*'
      fileAddedMessage='Started!'
      completedMessage='Complete!'
      service={constants.API_URL + 'store-neural-network-model'}
      textLabel='Выбор модели:'
      disableDragAndDrop={true}
      onFileSuccess={(file, message) => {
        console.log('здесь ошибка')
      }}
      onFileAdded={(file, resumable) => {
        const input = document.getElementsByClassName('btn file-upload')
        input[0].style.display = 'none'
        const btnStart = document.getElementsByClassName('btn start')
        btnStart[0].removeAttribute('disabled')
      }}
      maxFiles={1}
      startButton={true}
      onStartUpload={() => {
        const btnStart = document.getElementsByClassName('btn start')
        btnStart[0].style.display = 'none'
        const list = document.getElementsByClassName('resumable-list')
        list[0].style.display = 'none'
      }}
    />
  )
}
