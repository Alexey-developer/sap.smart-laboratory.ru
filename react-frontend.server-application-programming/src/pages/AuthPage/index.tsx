import React, { useRef, useEffect } from 'react'

import { SetPageTitle } from '@utils/SetPageTitle'
import constants from '@utils/constants.json'

import axios from 'axios'

import { useReactive } from 'ahooks'

import { useNavigate } from 'react-router-dom'

import {
  Container,
  Row,
  Col,
  Form,
  Button,
  Badge,
  InputGroup,
  Alert,
} from 'react-bootstrap'

export const AuthPage: React.FC = () => {
  SetPageTitle('Авторизация')

  const navigate = useNavigate()

  const state = useReactive({
    error: '',
  })

  const emailInputRef = useRef<HTMLInputElement>(null)
  const passwordInputRef = useRef<HTMLInputElement>(null)

  const authorize = async () => {
    try {
      state.error = ''
      const { data } = await axios.post(constants.API_URL + 'login', {
        email: emailInputRef.current?.value,
        password: passwordInputRef.current?.value,
      })
      localStorage.setItem('auth_key', data.access_token)
      localStorage.setItem('user_email', data.user.email)
      navigate('/')
    } catch (error) {
      if (!error.response) {
        state.error = 'Введите авторизационные данные'
      } else {
        state.error = error.response.data.message
      }
    }
  }

  useEffect(() => {
    if (localStorage.getItem('auth_key')) {
      navigate('/')
    }
  }, [])

  return (
    <Container>
      <Row>
        <Col>
          <h1 className='h3 text-center'>
            Инструмент №1, помогающий в разработке{' '}
            <Badge bg='dark'>
              <i className='fa-solid fa-chart-network'></i> нейронных сетей
            </Badge>
          </h1>

          <Form className='mt-5'>
            <Form.Group className='mb-3'>
              <Form.Label>E-mail</Form.Label>
              <InputGroup className='mb-3'>
                <InputGroup.Text>
                  <i className='fa-solid fa-envelope'></i>
                </InputGroup.Text>
                <Form.Control
                  ref={emailInputRef}
                  type='email'
                  placeholder='Введите E-mail'
                />
              </InputGroup>
            </Form.Group>
            <Form.Group className='mb-3'>
              <Form.Label>Пароль</Form.Label>
              <InputGroup className='mb-3'>
                <InputGroup.Text>
                  <i className='fa-solid fa-lock'></i>
                </InputGroup.Text>
                <Form.Control
                  ref={passwordInputRef}
                  type='password'
                  placeholder='Введите пароль'
                  autoComplete='on'
                />
              </InputGroup>
            </Form.Group>
            <Button
              className='d-block'
              variant='success'
              onClick={() => authorize()}
            >
              <i className='fa-solid fa-right-to-bracket'></i> Войти
            </Button>
            <Form.Text muted>
              *Если у Вас есть учетная запись - пожалуйста, введите свои
              авторизационные данные. Если Вы ещё не имеете таковой - введите
              регистрационные данные
            </Form.Text>
          </Form>
        </Col>
      </Row>
      <Row>
        <Col>
          {state.error && (
            <Alert className='mt-2' variant={'danger'}>
              <h3>
                <i className='fa-solid fa-hexagon-exclamation'></i>{' '}
                {state.error}
              </h3>
            </Alert>
          )}
        </Col>
      </Row>
    </Container>
  )
}
