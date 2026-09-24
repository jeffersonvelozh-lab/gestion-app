import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { AuthService } from '@core/services/auth.service';

describe('AuthService', () => {
  let service: AuthService;
  let http: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule, RouterTestingModule],
      providers: [AuthService],
    });
    service = TestBed.inject(AuthService);
    http = TestBed.inject(HttpTestingController);
    localStorage.clear();
  });

  afterEach(() => {
    http.verify();
    localStorage.clear();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('isAuthenticated() returns false when no token', () => {
    expect(service.isAuthenticated()).toBeFalse();
  });

  it('getToken() returns null when not logged in', () => {
    expect(service.getToken()).toBeNull();
  });

  it('login() stores token and emits user', () => {
    const mockResponse = {
      success: true,
      message: 'OK',
      data: {
        token: 'header.eyJleHAiOjk5OTk5OTk5OTl9.sig',
        refreshToken: 'refresh',
        user: { id: 1, name: 'Test', email: 'test@test.com', role: 'user' },
      },
    };

    let emittedUser: any;
    service.user$.subscribe(u => (emittedUser = u));

    service.login({ email: 'test@test.com', password: 'Password1' }).subscribe();

    const req = http.expectOne(r => r.url.includes('/auth/login'));
    req.flush(mockResponse);

    expect(service.getToken()).toBe(mockResponse.data.token);
    expect(emittedUser?.email).toBe('test@test.com');
  });

  it('logout() clears token and navigates', () => {
    localStorage.setItem('access_token', 'some-token');
    service.logout();
    expect(service.getToken()).toBeNull();
    expect(service.getCurrentUser()).toBeNull();
  });
});
