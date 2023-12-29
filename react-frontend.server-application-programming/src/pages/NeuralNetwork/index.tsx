//@ts-nocheck

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
  OverlayTrigger,
  Tooltip,
  Form,
  Modal,
  InputGroup,
} from 'react-bootstrap'

import { useParams, useNavigate } from 'react-router-dom'

import { useReactive } from 'ahooks'

import axios from 'axios'

import { Link } from 'react-router-dom'

import constants from '@utils/constants.json'
import { ResumableUploadModel } from '@components/ResumableUploadModel'

export const NeuralNetwork: React.FC = () => {
  const { id } = useParams()

  SetPageTitle('Нейронная сеть #' + id)

  const navigate = useNavigate()

  const nameInputRef = useRef<HTMLInputElement>(null)
  const imageInputRef = useRef<HTMLInputElement>(null)

  const state = useReactive({
    neuralNetwork: null,
    neuralNetworkEntities: [],
    model: null,
    requestIsMade: false,
    isRequesting: false,
    predictionResults: '',
    showModal: false,
    showModal2: false,
    error: '',
    error2: '',
    file: null,
  })

  const predict = async () => {
    state.error2 = ''
    state.predictionResults = ''
    state.isRequesting = true
    if (!imageInputRef.current?.files?.length) {
      state.error2 = 'Вы не выбрали изображение!'
    } else {
      state.file = imageInputRef.current?.files[0]
      try {
        const { data } = await axios.post(
          constants.API_URL + 'predict',
          {
            neural_network_id: id,
            image: state.file,
          },
          {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
              'Content-Type': 'multipart/form-data',
            },
          }
        )

        if (data.error) {
          console.log(data.error)
        } else {
          state.predictionResults = data.prediction_output
        }
      } catch (error) {
        state.error2 = error.response.data.message
      }
    }

    state.isRequesting = false
  }

  const storeNeuralNetworkEntity = async () => {
    try {
      state.error = ''
      const { data } = await axios.post(
        constants.API_URL + 'store-neural-network-entity',
        {
          neural_network_id: id,
          name: nameInputRef.current?.value,
        },
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
      } else {
        state.showModal = false
        getNeuralNetworkById()
      }
    } catch (error) {
      state.error = error.response.data.message
    }
  }

  const deleteNeuralNetworkEntity = async id => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'delete-neural-network-entity',
        {
          id: id,
        },
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
      } else {
        getNeuralNetworkById()
      }
    } catch (error) {
      console.log(error)
    }
  }

  const getNeuralNetworkById = async () => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'get-user-neural-network-by-id',
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
        state.neuralNetwork = data.neural_network
        state.neuralNetworkEntities = data.classes
        state.model = data.model
        state.requestIsMade = true
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => {
    getNeuralNetworkById()
  }, [])

  return (
    <Container>
      <Row>
        <Col>
          {!state.model && state.requestIsMade && (
            <ResumableUploadModel nnId={id} />
          )}
        </Col>
      </Row>
      <Row>
        <Col>
          <Button
            className='mt-5'
            variant='success'
            onClick={() => {
              state.showModal = true
            }}
          >
            <i className='fa-solid fa-plus'></i> Создать новый класс
          </Button>

          <Modal
            show={state.showModal}
            onHide={() => {
              state.showModal = false
            }}
          >
            <Modal.Header closeButton>
              <Modal.Title>Создать новый класс в рамках нейросети</Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <Form>
                <Form.Group className='mt-3'>
                  <Form.Label>Название</Form.Label>
                  <InputGroup className='mb-3'>
                    <InputGroup.Text>
                      <i className='fa-solid fa-file-signature'></i>
                    </InputGroup.Text>
                    <Form.Control
                      ref={nameInputRef}
                      type='text'
                      placeholder='Введите название класса'
                    />
                  </InputGroup>
                </Form.Group>
              </Form>
              {state.error && (
                <Alert className='mt-2' variant={'danger'}>
                  <h3>
                    <i className='fa-solid fa-hexagon-exclamation'></i>{' '}
                    {state.error}
                  </h3>
                </Alert>
              )}
            </Modal.Body>
            <Modal.Footer>
              <Button variant='primary' onClick={storeNeuralNetworkEntity}>
                <i className='fa-solid fa-plus'></i> Создать
              </Button>
            </Modal.Footer>
          </Modal>

          {state.model && state.requestIsMade && (
            <Button
              className='mt-5 ms-3'
              variant='success'
              onClick={() => {
                state.showModal2 = true
              }}
            >
              <i className='fa-solid fa-percent'></i> Предсказание
            </Button>
          )}

          <Modal
            show={state.showModal2}
            onHide={() => {
              state.showModal2 = false
            }}
          >
            <Modal.Header closeButton>
              <Modal.Title>
                Новое предсказание на существующей модели
              </Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <Form>
                <Form.Group className='mt-3'>
                  <Form.Label>Изображение</Form.Label>
                  <Form.Control
                    ref={imageInputRef}
                    type='file'
                    accept='image/*'
                  />
                </Form.Group>
              </Form>
              {state.error2 && (
                <Alert className='mt-2' variant={'danger'}>
                  <h3>
                    <i className='fa-solid fa-hexagon-exclamation'></i>{' '}
                    {state.error2}
                  </h3>
                </Alert>
              )}

              {state.predictionResults && (
                <Alert className='mt-2' variant={'danger'}>
                  {Object.keys(state.predictionResults).map(index => (
                    <h5
                      key={index}
                      dangerouslySetInnerHTML={{
                        __html: state.predictionResults[index],
                      }}
                    ></h5>
                  ))}
                </Alert>
              )}
            </Modal.Body>
            <Modal.Footer>
              {!state.isRequesting ? (
                <Button variant='primary' onClick={predict}>
                  <i className='fa-solid fa-plus'></i> Предсказать
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
                  Выполняется предсказание...
                </Button>
              )}
            </Modal.Footer>
          </Modal>
        </Col>
      </Row>
      <Row>
        <Col>
          {state.neuralNetwork === null ? (
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
                Получение Вашей нейронной сети...
              </h5>
            </Alert>
          ) : (
            <Alert className='mt-5' variant={'success'}>
              #{id} {state.neuralNetwork.name}
            </Alert>
          )}
        </Col>
      </Row>
      <h2 className='mt-5'>Классы нейросети</h2>
      {Object.keys(state.neuralNetworkEntities).map(index => (
        <Row className='mb-5' key={state.neuralNetworkEntities[index].id}>
          <Col>
            <Card>
              <Card.Header>
                <Card.Title>
                  #
                  {state.neuralNetworkEntities[index].id +
                    ' ' +
                    state.neuralNetworkEntities[index].name}
                </Card.Title>
              </Card.Header>
              <Card.Body>
                <Card.Text>
                  Создан:{' '}
                  {new Date(
                    state.neuralNetworkEntities[index].created_at
                  ).toLocaleString('ru', { timeZone: 'Europe/Moscow' })}
                </Card.Text>
              </Card.Body>
              <Card.Footer>
                <Link to={'/classes/' + state.neuralNetworkEntities[index].id}>
                  <Button variant='primary'>
                    <i className='fa-solid fa-arrow-right'></i> Открыть
                  </Button>
                </Link>
                <OverlayTrigger
                  placement='top'
                  overlay={<Tooltip>Удаление класса</Tooltip>}
                >
                  <Button
                    className='float-end'
                    variant='danger'
                    onClick={() =>
                      deleteNeuralNetworkEntity(
                        state.neuralNetworkEntities[index].id
                      )
                    }
                  >
                    <i className='fa-solid fa-trash-xmark'></i>
                  </Button>
                </OverlayTrigger>
              </Card.Footer>
            </Card>
          </Col>
        </Row>
      ))}
      {!state.neuralNetworkEntities.length && (
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
              ? 'Получение классов Вашей нейронной сети...'
              : 'У Вас ещё нет классов в Вашей нейронной сети'}
          </h5>
        </Alert>
      )}
    </Container>
  )
}
