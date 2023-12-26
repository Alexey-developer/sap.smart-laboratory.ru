import React, { useEffect, useRef } from 'react'

import { SetPageTitle } from '@utils/SetPageTitle'

import {
  Container,
  Row,
  Col,
  Alert,
  Spinner,
  Card,
  Button,
  Badge,
} from 'react-bootstrap'

import { useParams, useNavigate } from 'react-router-dom'

import { useReactive } from 'ahooks'

import axios from 'axios'

import constants from '@utils/constants.json'

export const Entity: React.FC = () => {
  const { id } = useParams()

  SetPageTitle('Нейронная сеть #' + id)

  const navigate = useNavigate()

  const state = useReactive({
    entity: null,
    images: [],
    requestIsMade: false,
    downloadUrl: '',
    archiving: false,
  })

  const downloadImages = async () => {
    state.downloadUrl = ''
    state.archiving = true
    try {
      const { data } = await axios.post(
        constants.API_URL + 'download-entity-images',
        { id: id },
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
      } else {
        console.log(data)

        state.downloadUrl = data.download_url
        state.archiving = false
      }
    } catch (error) {
      console.log(error)
    }
  }

  const getEntityById = async () => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'get-entity-by-id',
        { id: id },
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
        navigate('/neural-networks')
      } else {
        state.entity = data.entity
        state.images = data.images
        state.requestIsMade = true
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => {
    getEntityById()
  }, [])

  return (
    <Container>
      <Row>
        <Col>
          {state.entity === null ? (
            <Alert className='mt-5' variant={'primary'}>
              <h5>
                <Spinner
                  className='me-2'
                  as='span'
                  animation='grow'
                  size='sm'
                  role='status'
                  aria-hidden='true'
                />
                Получение класса...
              </h5>
            </Alert>
          ) : (
            <Alert className='mt-5' variant={'success'}>
              #{id} {state.entity.name}
            </Alert>
          )}
        </Col>
      </Row>

      {state.images.length ? (
        !state.archiving ? (
          <Button className='mt-5' variant='warning' onClick={downloadImages}>
            <i className='fa-solid fa-file-zip'></i> Сформировать ссылку на
            скачивание
          </Button>
        ) : (
          <Button className='mb-2' variant='danger' disabled>
            <Spinner
              as='span'
              animation='grow'
              size='sm'
              role='status'
              aria-hidden='true'
            />{' '}
            Формируем...
          </Button>
        )
      ) : (
        ''
      )}

      {state.downloadUrl !== '' && (
        <a target='_blank' href={state.downloadUrl} download>
          <Button className='mt-5 ms-2' variant='success'>
            <i className='fa-solid fa-file-zip'></i> Скачать архив датасета
          </Button>
        </a>
      )}

      <h2 className='mt-5'>Датасет класса (изображения)</h2>
      <Row>
        {Object.keys(state.images).map(index => (
          <Col key={state.images[index].id} xs={6} md={4}>
            <Card className='mb-2'>
              <Card.Img
                variant='top'
                src={state.images[index].small_size_url}
              />
              <Card.Body>
                <Card.Title>
                  <Badge className='me-2' bg='info'>
                    (#{state.images[index].id})
                  </Badge>
                  {state.images[index].name}
                </Card.Title>
                <Button
                  href={state.images[index].webpage_url}
                  target='_blank'
                  variant='primary'
                >
                  Перейти
                </Button>
              </Card.Body>
            </Card>
          </Col>
        ))}
      </Row>

      {!state.images.length && (
        <Alert variant={'primary'}>
          <h5>
            {!state.requestIsMade ? (
              <Spinner
                className='me-2'
                as='span'
                animation='grow'
                size='sm'
                role='status'
                aria-hidden='true'
              />
            ) : (
              <i className='fa-solid fa-empty-set me-2'></i>
            )}
            {!state.requestIsMade
              ? 'Получение датасета класса...'
              : 'Датасет класса отсутствует'}
          </h5>
        </Alert>
      )}
    </Container>
  )
}
