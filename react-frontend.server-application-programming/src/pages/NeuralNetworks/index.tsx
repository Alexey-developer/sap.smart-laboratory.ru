//@ts-nocheck

import React, { useEffect, useRef } from 'react'

import { SetPageTitle } from '@utils/SetPageTitle'

import axios from 'axios'

import constants from '@utils/constants.json'

import {
  Container,
  Row,
  Col,
  Form,
  InputGroup,
  Tooltip,
  Button,
  Card,
  Spinner,
  Alert,
  OverlayTrigger,
  Modal,
} from 'react-bootstrap'

import { useReactive } from 'ahooks'

import { Link } from 'react-router-dom'

export const NeuralNetworks: React.FC = () => {
  SetPageTitle('Мои нейросети')

  const nameInputRef = useRef<HTMLInputElement>(null)
  const descriptionInputRef = useRef<HTMLInputElement>(null)

  const state = useReactive({
    neuralNetworks: [],
    requestIsMade: false,
    showModal: false,
    error: '',
  })

  const getNeuralNetworks = async () => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'get-user-neural-networks',
        {},
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
      } else {
        state.neuralNetworks = data
        state.requestIsMade = true
      }
    } catch (error) {
      console.log(error)
    }
  }

  const storeNeuralNetwork = async () => {
    try {
      state.error = ''
      const { data } = await axios.post(
        constants.API_URL + 'store-neural-network',
        {
          name: nameInputRef.current?.value,
          description: descriptionInputRef.current?.value,
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
        getNeuralNetworks()
      }
    } catch (error) {
      state.error = error.response.data.message
    }
  }

  const deleteNeuralNetwork = async id => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'delete-neural-network',
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
        getNeuralNetworks()
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => {
    getNeuralNetworks()
  }, [])

  return (
    <>
      <Container>
        <Row>
          <Col>
            <Button
              className='mt-5'
              variant='success'
              onClick={() => {
                state.showModal = true
              }}
            >
              <i className='fa-solid fa-plus'></i> Создать
            </Button>

            <Modal
              show={state.showModal}
              onHide={() => {
                state.showModal = false
              }}
            >
              <Modal.Header closeButton>
                <Modal.Title>Создать новую нейросеть</Modal.Title>
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
                        placeholder='Введите название нейросети'
                      />
                    </InputGroup>
                  </Form.Group>
                  <Form.Group className='mt-3'>
                    <Form.Label>Описание</Form.Label>
                    <InputGroup className='mb-3'>
                      <InputGroup.Text>
                        <i className='fa-solid fa-comment'></i>
                      </InputGroup.Text>
                      <Form.Control
                        ref={descriptionInputRef}
                        type='text'
                        placeholder='Введите описание нейросети'
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
                <Button variant='primary' onClick={storeNeuralNetwork}>
                  <i className='fa-solid fa-plus'></i> Создать
                </Button>
              </Modal.Footer>
            </Modal>
          </Col>
        </Row>
        {Object.keys(state.neuralNetworks).map(index => (
          <Row key={state.neuralNetworks[index].id}>
            <Col className='mt-5'>
              <Card>
                <Card.Header>
                  <Card.Title>
                    #
                    {state.neuralNetworks[index].id +
                      ' ' +
                      state.neuralNetworks[index].name}
                  </Card.Title>
                </Card.Header>
                <Card.Body>
                  <Card.Text>
                    Описание: {state.neuralNetworks[index].description}
                  </Card.Text>
                  <Card.Text>
                    Создана:{' '}
                    {new Date(
                      state.neuralNetworks[index].created_at
                    ).toLocaleString('ru', { timeZone: 'Europe/Moscow' })}
                  </Card.Text>
                </Card.Body>
                <Card.Footer>
                  <Link
                    to={'/neural-networks/' + state.neuralNetworks[index].id}
                  >
                    <Button variant='primary'>
                      <i className='fa-solid fa-arrow-right'></i> Открыть
                    </Button>
                  </Link>
                  <OverlayTrigger
                    placement='top'
                    overlay={<Tooltip>Удаление нейросети</Tooltip>}
                  >
                    <Button
                      className='float-end'
                      variant='danger'
                      onClick={() =>
                        deleteNeuralNetwork(state.neuralNetworks[index].id)
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
        {!state.neuralNetworks.length && (
          <Alert className='mt-5' variant={'primary'}>
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
                ? 'Получение Ваших нейронных сетей...'
                : 'У Вас ещё нет нейронных сетей'}
            </h5>
          </Alert>
        )}
      </Container>
    </>
  )
}

// : {
// 					id: string
// 					data: { origUrl: string; snippet: { title: string; url: string } }
// 				}[]
