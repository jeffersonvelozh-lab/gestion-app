using AuthService.Data;
using AuthService.DTOs;
using AuthService.Services;
using FluentAssertions;
using Microsoft.EntityFrameworkCore;
using Moq;
using Xunit;

namespace AuthService.Tests;

public class AuthServiceTests
{
    private static AppDbContext CreateInMemoryDb()
    {
        var options = new DbContextOptionsBuilder<AppDbContext>()
            .UseInMemoryDatabase(Guid.NewGuid().ToString())
            .Options;
        return new AppDbContext(options);
    }

    private static ITokenService CreateTokenServiceMock()
    {
        var mock = new Mock<ITokenService>();
        mock.Setup(t => t.GenerateAccessToken(It.IsAny<Models.User>())).Returns("mock-access-token");
        mock.Setup(t => t.GenerateRefreshToken()).Returns("mock-refresh-token");
        return mock.Object;
    }

    [Fact]
    public async Task Register_WithValidData_ReturnsAuthResponse()
    {
        using var db = CreateInMemoryDb();
        var service = new Services.AuthService(db, CreateTokenServiceMock());

        var result = await service.RegisterAsync(new RegisterRequest("Juan", "juan@test.com", "Password123"));

        result.Should().NotBeNull();
        result.Token.Should().Be("mock-access-token");
        result.User.Email.Should().Be("juan@test.com");
    }

    [Fact]
    public async Task Register_WithDuplicateEmail_ThrowsInvalidOperationException()
    {
        using var db = CreateInMemoryDb();
        var service = new Services.AuthService(db, CreateTokenServiceMock());

        await service.RegisterAsync(new RegisterRequest("Juan", "juan@test.com", "Password123"));

        var act = () => service.RegisterAsync(new RegisterRequest("Otro", "juan@test.com", "Password456"));
        await act.Should().ThrowAsync<InvalidOperationException>();
    }

    [Fact]
    public async Task Login_WithValidCredentials_ReturnsAuthResponse()
    {
        using var db = CreateInMemoryDb();
        var service = new Services.AuthService(db, CreateTokenServiceMock());
        await service.RegisterAsync(new RegisterRequest("Juan", "juan@test.com", "Password123"));

        var result = await service.LoginAsync(new LoginRequest("juan@test.com", "Password123"));

        result.Should().NotBeNull();
        result.Token.Should().Be("mock-access-token");
    }

    [Fact]
    public async Task Login_WithWrongPassword_ThrowsUnauthorizedException()
    {
        using var db = CreateInMemoryDb();
        var service = new Services.AuthService(db, CreateTokenServiceMock());
        await service.RegisterAsync(new RegisterRequest("Juan", "juan@test.com", "Password123"));

        var act = () => service.LoginAsync(new LoginRequest("juan@test.com", "WrongPassword"));
        await act.Should().ThrowAsync<UnauthorizedAccessException>();
    }

    [Fact]
    public async Task Login_WithNonExistentEmail_ThrowsUnauthorizedException()
    {
        using var db = CreateInMemoryDb();
        var service = new Services.AuthService(db, CreateTokenServiceMock());

        var act = () => service.LoginAsync(new LoginRequest("noexiste@test.com", "Password123"));
        await act.Should().ThrowAsync<UnauthorizedAccessException>();
    }
}
