using AuthService.Models;
using Microsoft.EntityFrameworkCore;

namespace AuthService.Data;

public class AppDbContext(DbContextOptions<AppDbContext> options) : DbContext(options)
{
    public DbSet<User> Users => Set<User>();

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<User>(entity =>
        {
            entity.ToTable("Users");

            entity.HasKey(u => u.Id);

            entity.Property(u => u.Id)
                  .HasColumnName("id");

            entity.Property(u => u.Name)
                  .HasColumnName("name")
                  .IsRequired()
                  .HasMaxLength(100);

            entity.Property(u => u.Email)
                  .HasColumnName("email")
                  .IsRequired()
                  .HasMaxLength(150);

            entity.HasIndex(u => u.Email).IsUnique();

            entity.Property(u => u.PasswordHash)
                  .HasColumnName("password_hash")
                  .IsRequired();

            entity.Property(u => u.Role)
                  .HasColumnName("role")
                  .HasDefaultValue("user")
                  .HasMaxLength(20);

            entity.Property(u => u.IsActive)
                  .HasColumnName("is_active")
                  .HasDefaultValue(true);

            entity.Property(u => u.CreatedAt)
                  .HasColumnName("created_at")
                  .HasDefaultValueSql("NOW()");

            entity.Property(u => u.UpdatedAt)
                  .HasColumnName("updated_at")
                  .HasDefaultValueSql("NOW()");
        });
    }
}
