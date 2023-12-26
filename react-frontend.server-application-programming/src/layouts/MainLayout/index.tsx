import React, { useEffect } from 'react'

import './reset.css'

import 'bootstrap/dist/css/bootstrap.min.css'

import '@assets/fontawesome/v6.5.1/css/all.css'
import '@assets/fontawesome/v6.5.1/css/sharp-thin.css'
import '@assets/fontawesome/v6.5.1/css/sharp-solid.css'
import '@assets/fontawesome/v6.5.1/css/sharp-regular.css'
import '@assets/fontawesome/v6.5.1/css/sharp-light.css'

import { Outlet } from 'react-router-dom'

import { useNavigate } from 'react-router-dom'

import { Container, Navbar, Nav } from 'react-bootstrap'

import { Link } from 'react-router-dom'

export const MainLayout: React.FC = () => {
  const navigate = useNavigate()

  useEffect(() => {
    if (!localStorage.getItem('auth_key')) {
      navigate('/auth')
    }
  }, [])

  return (
    <div className='wrapper'>
      <Navbar expand='lg' className='bg-body-tertiary'>
        <Container>
          <Link to='/' className='text-decoration-none mx-1'>
            <Navbar.Brand className='fw-bold'>
              <i className='fa-solid fa-chart-network'></i> NN-Helper
            </Navbar.Brand>
          </Link>

          <Navbar.Toggle aria-controls='basic-navbar-nav' />
          <Navbar.Collapse
            id='basic-navbar-nav'
            className='justify-content-end'
          >
            <Nav className='me-auto'>
              <Link to='/neural-networks' className='text-decoration-none mx-1'>
                <Navbar.Text>Мои нейросети</Navbar.Text>
              </Link>
              <Link
                to='/dataset-generation'
                className='text-decoration-none mx-1'
              >
                <Navbar.Text>Генерация датасета</Navbar.Text>
              </Link>
            </Nav>
            <Navbar.Text>
              Авторизован как:
              <span className='text-decoration-underline ms-1 me-2'>
                {localStorage.getItem('user_email')}
              </span>
              <span
                role='button'
                className='fw-bold'
                onClick={() => {
                  localStorage.removeItem('auth_key')
                  localStorage.removeItem('user_email')
                  navigate('/auth')
                }}
              >
                Выйти
              </span>
            </Navbar.Text>
          </Navbar.Collapse>
        </Container>
      </Navbar>
      <Outlet />
    </div>
  )
}
